
var MysqlConsole = function (ElementID, WampURL, WampRealm){
    console.log('Creating new MysqlConsole object: ' + ElementID);
    autobahn.Connection.call(this, {
            url: WampURL,
            realm: WampRealm
    });
  
    this.rootElement = $('#'+ElementID);
    this.ElementID = ElementID;
    this.strblob = '';
    this.commandHistory = [''];
    this.commandHistoryIndex = 0;

    this.$().addClass('MysqlConsole');
};

MysqlConsole.prototype = Object.create(autobahn.Connection.prototype);
MysqlConsole.prototype.constructor = MysqlConsole;

var p = MysqlConsole.prototype;

p.$ = function(selector){
    if (selector === undefined) {
        return this.rootElement;
    }

    return $(selector, this.rootElement)
};

p.lockInput = function(){
    console.log('locking input');
    this.$("input.mysql-console-input").attr('readonly','');
    this.$('input.mysql-console-input').off('keydown');
}

p.newPrompt = function (){
    this.$("input.mysql-console-input").addClass("mysql-console-history")
      .removeClass("mysql-console-input");

    this.$().append('<div class="mysql-console-line request">' +
        '<div class="prompt">prompt</div>' +
        '<input class="mysql-console-input request"></input></div>');

    this.$("input.mysql-console-input").keydown(this.enterAction.bind(this)).focus();
}

p.startWaiting = function (){
    var dots = '.';

    this.$().append("<div class='mysql-console-line pending'>.</div>");

    return setInterval(function(){
        this.$("div.pending").text(dots);
        dots += '.';
    }, 100);
}

p.stopWaiting = function (dotTimer){
   clearInterval(dotTimer);
   this.$("div.pending").remove();
}

p.insertLine = function (text, classes, pre){
    classes = classes === undefined ? [] : classes;
    pre = pre === undefined ? false : pre;

    if(pre){
        console.time('result insert');
        this.$().append('<div class="mysql-console-line inserting ' + 
          classes.join(' ') + '"><pre></pre></div>');
        //this.$('div.inserting pre').get(0).appendChild(document.createTextNode(text));
        //this.$('div.inserting pre').show();
        this.$('div.inserting pre').text(text);
        console.timeEnd('result insert');
    }else{
        this.$().append('<div class="mysql-console-line inserting ' + 
          classes.join(' ') + '"></div>');
        this.$('div.inserting').text(text);
    }

    this.$('div.inserting').removeClass("inserting");
}

p.insertResponse = function (res, then){
    then = then === undefined ? function(){} : then;

    if (res.error !== undefined){
        console.log('inserting error');
        this.insertLine(res.error, ['error']);
        then();
    }else if (res.columnNames !== undefined && res.rows !== undefined){
        console.log('normal result recieved');
        var worker = new Worker('/javascript/formatTableData.js'); 
 
        worker.onmessage = function(event){
            console.log('inserting normal result');
            this.insertLine(event.data, ['result'], true);
            then();
        }.bind(this)
    
        worker.onerror = function(event){
            console.log('worker failed to format table data!!!');
            this.insertLine('Query result could not be formated!!!', ['error']);
            then();
        }.bind(this)

        console.log('spinning off worker to fromat result');
        worker.postMessage(res);
/*
        res.rows.forEach(function (row){
            this.insertLine(row.join(), ['result'], false);
        }.bind(this));
        then();
*/
    }else{
        console.log('result in unknown format!!!');
        this.insertLine(res, ['result']);
        then();
    }
 
}

p.displayResults = function (sqlArray, i){
    if(sqlArray.length > i){
        console.log(i + 'requesting: ' + sqlArray[i]);
        var timer = this.startWaiting();
        this.sqlSession.call('com.mysql.console.query', 
          [this.sqlSessionName, sqlArray[i]]).then(function(res){
              this.stopWaiting(timer);
              this.insertResponse(JSON.parse(res), function(){
                  this.displayResults(sqlArray, i + 1);
              }.bind(this)); 
          }.bind(this),function(err){
              console.log(err);
              this.stopWaiting(timer);
              this.insertResponse({error:'Could not perform query, check console for more info.'}); 
          }.bind(this));
    }else{
        this.newPrompt();
    }
}

p.enterAction = function (event){
    //on enter
    if(event.keyCode === 13){
        var timer = null; 
        //$(event.target).off(event)
        this.lockInput();

        if (this.$("input.mysql-console-input").val() === "exit"){
            timer = this.startWaiting();
            clearTimeout(this.watchdogTimer);
            this.sqlSession.call('com.mysql.console.closeSession', 
              [this.sqlSessionName, false]).then(function(res){
                this.stopWaiting(timer);
                this.insertResponse(res);
            }.bind(this));
            return;
        }
        var command = this.$("input.mysql-console-input").val();

        this.commandHistory[this.commandHistory.length - 1] = command;
        this.commandHistory.push('');
        this.commandHistoryIndex = this.commandHistory.length - 1;

        this.strblob += command;
            
        console.log('mysqlConsole enter occured: ' + this.strblob);
        var pos = this.strblob.search(/[^;]*$/);
        if(pos != 0){
            console.log(pos);
            sqlStmts = this.strblob.slice(0,pos);
            this.strblob = this.strblob.slice(pos, -1);
          
            console.log('session name: ' +  this.sqlSessionName);

            sqlArray = sqlStmts.split(';');
            sqlArray.pop();
            this.displayResults(sqlArray, 0);
        }else{
            console.log('line blobbed: ' + pos + ' ' + this.strblob);
            this.strblob += ' ';
            this.newPrompt();
        }
    }
    //if up arrow
    else if(event.keyCode === 38){
        this.commandHistoryIndex -= 1;
        if(this.commandHistoryIndex >= 0){
            this.commandHistory[this.commandHistoryIndex + 1] = 
              this.$("input.mysql-console-input").val();

            this.$("input.mysql-console-input").val(this.commandHistory[this.commandHistoryIndex]);
        }else{
            this.commandHistoryIndex = 0;
        }
    }
    //if down arrow
    else if(event.keyCode === 40){
        this.commandHistoryIndex += 1;
 
        if(this.commandHistoryIndex < this.commandHistory.length){
            this.commandHistory[this.commandHistoryIndex - 1] = 
              this.$("input.mysql-console-input").val();
            this.$("input.mysql-console-input").val(this.commandHistory[this.commandHistoryIndex]);
        }else{
            this.commandHistoryIndex = this.commandHistory.length - 1;
        }
    }
}

p.onopen = function (session) {
    this.sqlSession = session;
    console.log('wamp connection open');


    console.log('creating session');
    var tmpTimer = this.startWaiting(); 

    var accountSessionName = $.cookie('session_id');
    var accountSessionEncPW = $.cookie('enc_pw'); 
    console.log('creating session');

    session.call('com.mysql.console.requestSession', [accountSessionName, accountSessionEncPW])
      .then(function (sessionName) {
        this.stopWaiting(tmpTimer);

        session.call('com.mysql.console.giveBone', [sessionName]).then(function (timeoutLength){
            console.log('initial timeoutLength = ' + timeoutLength);
            this.newPrompt();
            foodChain.bind(this)('com.mysql.console.giveBone', [sessionName], timeoutLength);
        }.bind(this), function (err){
            console.log(err);
            this.stopWaiting(tmpTimer);
            this.lockInput();
            this.insertResponse({error:'Could not lease session.  Expect to be timed out.'});
        }.bind(this));

        
        function foodChain(rpc, args, timeoutLength){
            console.log('foodChain: ' + rpc + ' ' + args + ' ' + timeoutLength);
            
            if(timeoutLength < 0){
                console.log('Lease refuesed in foodchain!!!');
                this.lockInput();
                this.insertResponse({error: 'giveBone returned with ' + timeoutLength + 
                  '!!! Session has been closed on Server.'}); 
                return;
            }
 
            session.call(rpc, args).then(function (newTimeout){
                this.watchdogTimer = setTimeout(foodChain.bind(this), 
                 timeoutLength*500, rpc, args, newTimeout);
            }.bind(this),function (err) {
                console.log(err);
                this.stopWaiting(tmpTimer);
                this.lockInput();
                this.insertResponse({error:'Server does not have giveBone registered!!!'});
            }.bind(this));
        }
        
        console.log('recieved session name: ' + sessionName);
        this.sqlSessionName = sessionName;
       
        console.log('foodChain delegated');
            
    }.bind(this), function (err) {
        console.log(err);
        this.lockInput();
        this.stopWaiting(tmpTimer);
        this.insertResponse({error:"Session request rejected.  For more info check the console."});
    }.bind(this));

    console.log('wamp event registration complete');
}

p = undefined;

/*
$(document).ready(function(){
    console.log('page ready');
    mysqlConsole = new MysqlConsole('testConsole', 'ws://localhost:8081/ws', 'realm1')
    mysqlConsole.open();
});
*/
 
