console.log('event registartion begun');

var conn = new autobahn.Connection({
        url: 'ws://localhost:8081/ws',
        realm: 'realm1'
    });

var thisSessionName = -1;
var strblob = '';
var globalSession = null;
var hunger = null;
var watchdogTimer = null;

function lockInput(){
    console.log('locking input');
    $("input.mysql-console-input").attr('readonly','');

}

function mysqlConsoleNewline(){
    $("input.mysql-console-input").addClass("mysql-console-history")
      .removeClass("mysql-console-input");

    $("#mysqlConsole").append('<div class="mysql-console-line request">' +
        '<div class="prompt">prompt</div>' +
        '<input class="mysql-console-input request"></input></div>');

    $("input.mysql-console-input").keydown(enterAction).focus();
}

function startWaiting(){
    var dots = '.';

    $("#mysqlConsole").append("<div class='mysql-console-line pending'>.</div>");

    return setInterval(function(){
        $("div.pending").text(dots);
        dots += '.';
    }, 100);
}

function stopWaiting(dotTimer){
   clearInterval(dotTimer);
   $("div.pending").remove();
}

function padValue(val, columnWidth){
    vals = String(val);
    

    while(vals.length < columnWidth){
        vals += ' ';
    }
    
    return vals;
}

function formatTableData(columnNames, rows){
    var res = '| '
  
    var columnWidths = [];

    columnNames.forEach(function (name, i){
        columnWidths[i] = name.length;
        rows.forEach(function (row, j){
            vals = String(row[i]);

            if(vals.length > columnWidths[i]){
                columnWidths[i] = vals.length;
            }
        });
    });

    console.log(columnWidths);

    columnNames.forEach(function (name, index){

        res += padValue(name, columnWidths[index]) + ' | ';
    });
    
    sep = '';
    while(sep.length < res.length - 1){
        sep += '-';
    }

    res += "\n" + sep;

    rows.forEach(function(row, index){
        res += "\n| ";
        row.forEach(function(val, index){
            res += padValue(val, columnWidths[index]) + ' | ';
        });
    });
    
    return res;
}

function insertResponse(res){
    if (res.error !== undefined){
        console.log('inserting error');
        $('#mysqlConsole').append('<div class="mysql-console-line error ' + 
          'inserting">' + res.error + '</div>');
    }else if (res.columnNames !== undefined && res.rows !== undefined){
        console.log('inserting normal result');
        $('#mysqlConsole').append('<div class="mysql-console-line result' + 
          ' inserting"><pre></pre></div>');
        $('#mysqlConsole div.mysql-console-line.inserting pre').
            text(formatTableData(res.columnNames, res.rows));
    }else{
        console.log('result in unknown format!!!');
        $('#mysqlConsole').append('<div class="mysql-console-line result' + 
          'inserting">' + res + '</div>');
    }

    //$('#mysqlConsole div.inserting pre').text(res);
 
    $('#mysqlConsole div.inserting').removeClass("inserting");
}

function displayResults (sqlArray, i){
    if(sqlArray.length > i){
        console.log(i + 'requesting: ' + sqlArray[i]);
        var timer = startWaiting();
        globalSession.call('com.mysql.console.query', 
          [thisSessionName, sqlArray[i]]).then(function(res){
              stopWaiting(timer);
              insertResponse(JSON.parse(res)); 
              displayResults(sqlArray, i + 1);
          },function(err){
              console.log(err);
              stopWaiting(timer);
              insertResponse({error:'Could not perform query, check console for more info.'}); 
          });
    }else{
        mysqlConsoleNewline();
    }
}

function enterAction (event){
    //on enter
    if(event.keyCode === 13){
        var timer = null; 
        $(this).off(event)
        lockInput();

        if ($("input.mysql-console-input").val() === "exit"){
            timer = startWaiting();
            clearTimeout(watchdogTimer);
            globalSession.call('com.mysql.console.closeSession', 
              [thisSessionName]).then(function(res){
                stopWaiting(timer);
                insertResponse(res);
            });
            return;
        }
        strblob += $("input.mysql-console-input").val();
            
          
        console.log('mysqlConsole enter occured: ' + strblob);
        var pos = strblob.search(/[^;]*$/);
        if(pos != 0){
            console.log(pos);
            sqlStmts = strblob.slice(0,pos);
            strblob = strblob.slice(pos, -1);
          
            console.log(sqlStmts);
            console.log('session name: ' +  thisSessionName);

            sqlArray = sqlStmts.split(';');
            sqlArray.pop();
            console.log(sqlArray); 
            displayResults(sqlArray, 0);
        }else{
            console.log(pos);
            console.log('line blobbed: ' + pos + ' ' + strblob);
            strblob += ' ';
            mysqlConsoleNewline();
        }
    }
}

conn.onopen = function (session) {
    globalSession = session;
    console.log('wamp connection open');


    console.log('creating session');
    var tmpTimer = startWaiting(); 

    //console.log('requesting session with login,\n id: ' + $.cookie('session_id') + " enc_pw: " + $.cookie('enc_pw'));
    var accountSessionName = $.cookie('session_id');
    var accountSessionEncPW = $.cookie('enc_pw'); 
    console.log('creating session');
    session.call('com.mysql.console.requestSession', [accountSessionName, accountSessionEncPW])
      .then(function (sessionName) {
        stopWaiting(tmpTimer);
        session.call('com.mysql.console.giveBone', [sessionName]).then(function (timeoutLength){
            console.log('initial timeoutLength = ' + timeoutLength);
            foodChain('com.mysql.console.giveBone', [sessionName], timeoutLength);
        }, function (err){
            console.log(err);
            stopWaiting(tmpTimer);
            lockInput();
            insertResponse({error:'Could not lease session.  Expect to be timed out.'});
        });

        
        function foodChain(rpc, args, timeoutLength){
            console.log('foodChain: ' + rpc + ' ' + args + ' ' + timeoutLength);
            
             
            session.call(rpc, args).then(function (newTimeout){
                watchdogTimer = setTimeout(foodChain, timeoutLength*500, rpc, args, newTimeout);
            },function (err) {
                console.log(err);
                stopWaiting(tmpTimer);
                lockInput();
                insertResponse({error:'session is gone'});
            });
        }
        
        console.log('recieved session name: ' + sessionName);
        thisSessionName = sessionName;
        $('input.mysql-console-input').keydown(enterAction);
        console.log('keydown event handler bound');
            
    }, function (err) {
        console.log(err);
        lockInput();
        stopWaiting(tmpTimer);
        insertResponse({error:"session request rejected.  For more info check the console."});
    });

    console.log('wamp event registration complete');
}


$(document).ready(function(){
    console.log('page ready');
    conn.open();
});
