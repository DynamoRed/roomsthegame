const canvas1 = document.getElementById("graph-1");
canvas1.width = 1000;
canvas1.height = 500;

const canvas2 = document.getElementById("graph-2");
canvas2.width = 1000;
canvas2.height = 500;

let graph1 = canvasContext(canvas1);
let graph2 = canvasContext(canvas2);

newAJAXBasicRequest("GET", "https://assets.roomsthegame.com/php/stats/get?stat=visitors", (response) => {
  let dataVisitors = JSON.parse(response);
  newAJAXBasicRequest("GET", "https://assets.roomsthegame.com/php/stats/get?stat=registrations", (response) => {
    let dataInscriptions = JSON.parse(response);

    drawGrids(graph1);
    drawAxis(graph1, dataVisitors, dataInscriptions);
    drawLineChart(graph1, dataVisitors, dataInscriptions);
  });
});

newAJAXBasicRequest("GET", "https://assets.roomsthegame.com/php/stats/get?stat=parties_played", (response) => {
  let dataGames = JSON.parse(response);
  
  drawGrids(graph2);
  drawAxis(graph2, dataGames);
  drawBarsChart(graph2, dataGames);
});

function canvasContext(canvas) {
  return canvas.getContext("2d");
}

function drawGrids(canvas) {
  let xGrid = 50;
  let cellSize = 50;
  canvas.beginPath();
  while (xGrid < canvas1.height) {
    canvas.moveTo(50, xGrid);
    canvas.lineTo(canvas1.width-50, xGrid);
    xGrid += cellSize;
  }

  canvas.strokeStyle = "gray";
  canvas.stroke();
}

function blocks(count) {
  return count * 10;
}

function getMaxValue(data1, data2) {
  let max1 = 0;
  for (let data in data1) {
    if (parseInt(data1[data]) > max1) max1 = parseInt(data1[data]);
  }
  if (data2) {
    let max2 = 0;
    for (let data in data2) {
      if (parseInt(data2[data]) > max2) max2 = parseInt(data2[data]);
    }
    if (max2 > max1) return max2;
  }
  return max1;
}

let q = 0.2;
function drawAxis(canvas, data1, data2) {
  let xPlot = 7.5;
  let yPlot = 45.5;
  let amount = 0;
  let step = 5;

  canvas.beginPath();
  canvas.lineWidth = 1.8;
  canvas.font = "20px montserrat";
  canvas.strokeStyle = "black";
  canvas.moveTo(blocks(7), blocks(5));
  canvas.lineTo(blocks(7), blocks(45));
  canvas.lineTo(blocks(75), blocks(45));
  canvas.moveTo(blocks(7), blocks(45));

  while (step * q * 8 < getMaxValue(data1, data2)) q++;

  for (let i = 0; i < 9; i++) {
    canvas.strokeText(amount, blocks(1), blocks(yPlot));
    yPlot -= 5;
    amount += step * q;
  }

  for (let data in data1) {
    canvas.strokeText(data, blocks(xPlot), blocks(47));
    xPlot += 10;
  }
  canvas.stroke();
}

function drawLineChart(canvas, data1, data2) {
  canvas.beginPath();
  canvas.moveTo(blocks(7), blocks(45));
  canvas.strokeStyle = "#BA181B";

  let xPlot = 10;

  for (let data in data1) {
    let data1InBlock = data1[data] / q;
    canvas.lineTo(blocks(xPlot), blocks(45 - data1InBlock));
    canvas.arc(blocks(xPlot), blocks(45 - data1InBlock), 2, 0, Math.PI * 2);
    xPlot += 10;
  }
  canvas.stroke();

  if (!data2) return;

  canvas.beginPath();
  canvas.moveTo(blocks(7), blocks(45));
  canvas.strokeStyle = "#ff9b13";
  xPlot = 10;

  for (let data in data2) {
    let data2InBlock = data2[data];
    canvas.lineTo(blocks(xPlot), blocks(45 - data2InBlock));
    canvas.arc(blocks(xPlot), blocks(45 - data2InBlock), 2, 0, Math.PI * 2);
    xPlot += 10;
  }
  canvas.stroke();
}

function drawBarsChart(canvas, data) {
  canvas.beginPath();
  canvas.lineWidth = 20;
  canvas.moveTo(blocks(7), blocks(45));
  canvas.strokeStyle = "#BA181B";

  let xPlot = 10;

  for (let k in data) {
    let dataInBlock = data[k] / q;
    canvas.moveTo(blocks(xPlot), blocks(44.9));
    canvas.lineTo(blocks(xPlot), blocks(45 - dataInBlock));
    xPlot += 10;
  }
  canvas.stroke();
}