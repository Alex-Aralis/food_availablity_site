console.log('event registartion begun');

var conn = new autobahn.Connection({
        url: 'ws://localhost:8081/ws',
        realm: 'realm1'
    });

var thisSessionName = -1;
var strblob = '';
var globalSession = null;

function mysqlConsoleNewline(){
    $("input.mysql-console-input").addClass("mysql-console-history")
      .removeClass("mysql-console-input").attr('readonly','');

    $("#mysqlConsole").append('<div class="mysql-console-line request">' +
        '<div class="prompt">prompt</div>' +
        '<input type="text" class="mysql-console-input">' +
        '</input>' +
      '</div>');
               
    $("input.mysql-console-input").keydown(enterAction).focus();
}

function startWaiting(){
    var dots = ''; 
            
    $("#mysqlConsole").append('<div class="mysql-console-line pending">' +
                           '.</div>');
            

     return setInterval(function(){
         dots += '.';
        $("div.pending").text(dots);
     }, 100);
            
}

function insertResponse(res){

    $("div.pending").html("<pre></pre>");

    $("div.pending").text(res).addClass("result")
      .removeClass("pending");
}

function enterAction (event){
    //on enter
    if(event.keyCode === 13){
        var timer = null;
        strblob += $("input.mysql-console-input").val() + ' ';
        $(this).off(event)
            
          
        console.log('mysqlConsole enter occured: ' + strblob);
        var pos = strblob.search(/[^a]*$/m);
        if(pos != 0){
            console.log(pos);
            sqlStmts = strblob.slice(0,pos);
            strblob = strblob.slice(pos, -1);
          
            console.log(sqlStmts);
            console.log('session name: ' +  thisSessionName);

            globalSession.call('com.mysql.console.query.' + thisSessionName, 
              [sqlStmts]).then(function(res){
                  clearInterval(timer);
                  insertResponse(res); 
                  mysqlConsoleNewline();
              });
              
            timer = startWaiting();
        }else{
            console.log(pos);
            console.log('line blobbed: ' + pos + strblob);
            mysqlConsoleNewline();
        }
    }
}

conn.onopen = function (session) {
    globalSession = session;
    console.log('wamp connection open');


    console.log('creating session');
    session.call('com.mysql.console.requestSession').then(function (sessionName) {
        console.log('recieved session name: ' + sessionName);
        thisSessionName = sessionName;
        $('input.mysql-console-input').keydown(enterAction);
        console.log('keydown event handler bound');
        
        console.log('registering pong');
        function pong(){
            /*
            console.log('pong');
            */
        }

        session.register('com.mysql.console.pong.' + sessionName, pong);
    });

    console.log('wamp event registration complete');
}


$(document).ready(function(){
    console.log('page ready');
    conn.open();
});
