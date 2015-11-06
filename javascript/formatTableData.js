
function padValue(val, columnWidth){
    var vals = String(val);
    

    while(vals.length < columnWidth){
        vals += ' ';
    }
    
    return vals;
}

self.onmessage = function formatTableData(msg){
    console.log(msg.data);
    var columnNames = msg.data.columnNames;
    var rows = msg.data.rows;
  
    var columnWidths = [];
    
    columnNames.forEach(function (name, i){
        columnWidths[i] = name.length;
        rows.forEach(function (row, j){
            var vals = String(row[i]);

            if(vals.length > columnWidths[i]){
                columnWidths[i] = vals.length;
            }
        });
    });

    console.log(columnWidths);
    
    var paddedColNames = [];
    columnNames.forEach(function (name, index){
        paddedColNames.push(padValue(name, columnWidths[index]));
    });
    
    colNamesJ = '| ' + paddedColNames.join(' | ') + ' |';
    var sep = [];
    while(sep.length < colNamesJ.length){
        sep.push('-');
    }
    
    var paddedRows = [];
    rows.forEach(function(row, index){
        var paddedRow = [];
        row.forEach(function(val, index){
            paddedRow.push(padValue(val, columnWidths[index]));
        });
        paddedRow = '| ' + paddedRow.join(' | ') + ' |';
        paddedRows.push(paddedRow);
    });
    
    postMessage([colNamesJ, sep.join(''), paddedRows.join("\n")].join("\n"));
}
