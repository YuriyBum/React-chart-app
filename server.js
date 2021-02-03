import express from 'express';
import fs from 'fs';
import path from 'path';
import React from 'react';
import ReactDOMServer from 'react-dom/server';
import App from '../src/App';

const PORTu = 3001;

let dataSourse = '/json';
let chartOffset = 0;
let pause = false;
let cArray = [], xArray = [];
let arr100to10Y = [];
let arr100to10X = [];
let avgY = chartOffset;
let avgX = chartOffset;
let timeToParse = 0;
let chartScaleY = 200;
let zeroArr = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
let checkSum = 0;


function FileReader () {
  localArray = zeroArr;
  try {
  fs.readFile(path.resolve('dataSourse'), 'utf-8', (err, data) => {
    if (err) {
      console.log(err);
    }
    else {
      try {
    let datArray = JSON.parse(data);
    localArray = datArray.positionY;
    let checkN = datArray.crc32;
    if (checkN == checkSum) {
      console.log("Совпадение данных!");
    }
    checkSum = checkN;
  } catch (err) {
    console.log(err);
  }
  }
  });
} catch (err) {
  console.log(err);
}
  return localArray;
}

function getavgY (myArray) {
  let allTotal = myArray.reduce((total, number) => {
return total + number;
}, 0);
  return (allTotal / myArray.length);
}

fs.readFile(path.resolve(dataSourse), 'utf-8', (err, data) => {
  if (err) {
    console.log(err);
    cArray = zeroArr;
    xArray = zeroArr;
  }
  else {
    try {
  let datArray = JSON.parse(data);
  console.log(datArray);
  cArray = datArray.positionY;
  xArray = datArray.positionX;
  console.log(cArray);
} catch (err) {
  console.log(err);
  cArray = zeroArr;
  xArray = zeroArr;
}
}
});




const WebSocket = require('ws');
const wss = new WebSocket.Server({ port: 3002 });
wss.on('connection', ws => {
  ws.on('message', message => {
    console.log(`Received message => ${message}`);
    if (message == "pause") {
      pause = !pause;
    } else {
      chartScaleY = parseInt(message);
      console.log("New scale: " + chartScaleY);
    }

  });

  ws.send(avgY);

  setInterval(function () {
    if (timeToParse >=31) {
  fs.readFile(path.resolve(dataSourse), 'utf-8', (err, data) => {
    if (err) {
      console.log(err);
      cArray = zeroArr;
      xArray = zeroArr;
    } else {
      try {
    let datArray = JSON.parse(data);
    cArray = datArray.positionY;
    xArray = datArray.positionX;
    //console.log(cArray);
  } catch (err) {
    console.log(err);
    cArray = zeroArr;
  }
  }
  });
  timeToParse = 0;
  }

  if (timeToParse < 32) {
    arr100to10Y.push(parseInt(cArray[timeToParse] * 1000));
    arr100to10X.push(parseInt(xArray[timeToParse] * 1000));
  }
  timeToParse++;

  if (arr100to10Y.length >= 10 || arr100to10X.length >= 10 ) {
  //  console.log(arr100to10Y);
     avgY = (getavgY (arr100to10Y) / 5 );
     avgX = (getavgY (arr100to10X) / 5 );
     if (avgY != avgY) avgY = 0;
     if (avgX != avgX) avgX = 0;
     if (!pause) ws.send(JSON.stringify(
       {
         posX: avgX,
         posY: avgY
       }
     ));

    arr100to10Y = [];
    arr100to10X = [];
  }

  }, 10);


  //setInterval(() => {  if (!pause) { ws.send(avgY);  }}, 100);

});


wss.on('message', (message) =>
  console.log('Message from server: ', message)
);


const app = express();

app.use('^/$', (req, res, next) => {
  fs.readFile(path.resolve('build/index.html'), 'utf-8', (err, data) => {
    if (err) {
      console.log(err);
      return res.status(500).send("Error with reading stage");
    }
    return res.send(data.replace('<div id="root"></div>', '<div id="root">{ReactDOMServer.renderToString(<App />)}</div>'));
  });
});


app.use('/json/', (req, res, next) => {
  fs.readFile(path.resolve('src/json/jsonfile_43.json'), 'utf-8', (err, data) => {
    if (err) {
      console.log(err);
      return res.status(500).send("Error with reading stage");
    }
    //console.log(data);

    return res.send(data.replace('<div id="root"></div>', '<div id="root">{ReactDOMServer.renderToString(<App />)}</div>'));
  });
});

app.use(express.static(path.resolve(__dirname, '..', 'build')));
app.listen(PORTu, () => {
  console.log(`App launched on ${PORTu}`);
});
