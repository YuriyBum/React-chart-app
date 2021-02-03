
<html>
<head>
<link rel="stylesheet" href="bootstrap.css" />
<link rel="stylesheet" href="style.css" />
<!-- Подключаем библиотеки React.js и Bootstrap-->
<link rel="icon" type="image/png" href="favicon.png">
    <script src="/module/js/react.development.js"></script>
    <script src="/module/js/react-dom.development.js"></script>
    <script src="/module/js/babel.min.js"></script>

	<script type="text/babel">
  //Пауза, меняется кнопкой. Код, который не должен выполняться на паузе, помечается if (!pause) { code }
	let pause = false;
  //Источник в формате json
  let dataSourse = "http://ocs.system-it.pro:482/json/jsonfile_43.json";
//Значения для формирования точек, переменные перезаполняются
 let avg = 0;
 let fastcurrentY = 0;
 let chartSpeed = 1;
 let currentDataY = [], startData = [];
 let currentDataX = [], startDataX = [];
 let chArchive = [];
 let pointposition = 100;
 let startValue = 270;
 let textFrame = 70;
 let frameHeight = 60;
 let cScale = 12;
 let scaleValueY = 200;

 const scree = window.innerWidth - (textFrame * 2);

 let chartLength = 1200;
 let chartsize = 1200;
//Функции изменения значений
function Pause () {
  if (pause)
  {
    pause = false;
    document.getElementById("pButton").innerHTML = "Остановить график";
}
  else
  {
    pause = true;
    console.log(chArchive);
    document.getElementById("pButton").innerHTML = "Возобновить график";
  }
}

function Scaler () {
 cScale = document.getElementById("scaleF").value;

  //chartLength = cScale * 20;
  chartSpeed = 12 / cScale;
//  console.log(chartLength);
  chartLength = chartsize/chartSpeed;
  startData = [];
  for (let u = 0; u < (chartLength -1); u++) {
   startData.push(0);
  }
//  console.log(startData);

}


function StartMover () {
//  console.log("Moved");
startValue = document.getElementById("startCh").value;
currentDataY = [];
//console.log(document.getElementById("startCh").value);
}

function ChangeDataSourse () {
  dataSourse = document.getElementById("dSourse").value;
}

/*function EnableScaling () {
  let scaleField = document.getElementById("scaleY");
  if(scaleField.disabled) {
    scaleField.disabled = false;
    autoScale = false;
  } else {
    scaleField.disabled = true;
    autoScale = true;
  }

}*/

function ScaleChartY () {
scaleValueY = document.getElementById("scaleY").value;
}

         window.onload = function()  {


           //console.log(scree);
           for (let  o = 0; o < 1200; o++)
 {
startData.push(0);
 }
//console.log(startData);
           //Наш график из точек
                            ReactDOM.render(
                                <LineChart />,
                                document.getElementById('linechart')
                              );

//Запрос для получения данных
          let ndt = document.getElementById('newData');
          let iv = 0;

                     let iu = 0;
                     //Первичная загрузка данных на старте
                     fetch(dataSourse)
                    .then(res => res.json())
                    .then((result) => {
                     //let chartDataY  = result.positionY;
                     //Значения для быстрого графика
                     currentDataY = [];
                     currentDataY = result.positionY;
                       },
                             (error) => {
                               console.log(error)
                      }
                          );


         	   setInterval(function() {
               if (iu >= 32) {
               fetch(dataSourse)
              .then(res => res.json())
              .then((result) => {
               //let chartDataY  = result.positionY;
               //Значения для быстрого графика
               currentDataY = [];
               currentDataY = result.positionY;
                 },
                       (error) => {
                         console.log(error)
                }
                    );

                 iu = 0;
               }

               if (iv < chartsize) {

                      iv += 2;
               }
              //Здесь ставим значение за последние 100мс
               if(iu < currentDataY.length && iu > 0) pointposition = (currentDataY[iu - 1] - currentDataY[iu]) * 100;
               //Если данные не удастся получить, условие не выполнится, т. к. будет NaN
                   if (pointposition == pointposition) {
                     //fastcurrentY = pointposition
                     chArchive.push(pointposition);

                     if(chArchive.length > 1200) {
                       chArchive.shift();
                     }

                     fastcurrentY = (210 * (parseInt(pointposition) / scaleValueY)) + parseInt(startValue);
                   }
                   else
                   {
                     fastcurrentY = 0;
                   }

              //    if (!pause) console.log("Point: " + pointposition );
              //  if(!pause) document.getElementById("last").innerHTML = fastcurrentY;

              iu++;

             }

               , 10);

                     iv +=2;

         		}

//Скрипт графика
            class LineChart extends React.Component {

              constructor(props) {
            super(props);
             this.state = {
             positionX: chartsize,
             positionY: [],
             maxPositions: 200,
             chartScale: 12,
             chartWidth: 1200,
             chartHeight: 420
             };
            };

            componentDidMount() {
              let positions = [];
              for(var k = 0; k < (chartLength - 1); k++) {
                positions.push(fastcurrentY);
              }

              //console.log(startData);
              //console.log(positions);
              this.setState({
                positionY: positions
              });
            //  if (!pause)  console.log(positions);
              setInterval(() => this.updatePosition(), 10);
                 }

                updatePosition() {
                  let positions = this.state.positionY;
                  if (positions.length >= chartLength) {
                    positions.shift();
                  }
                  positions.push(fastcurrentY);
                    //positions.push(180 * (parseInt(fastcurrentY)/ this.state.maxPositions)) + parseInt(startValue);
                //  if (!pause) console.log(itMax);
                  if (!pause){
                    if (positions.length != startData.length && startData.length > 0) {
                      this.setState({
                        positionY: startData,
                        maxPositions: scaleValueY,
                        chartScale: cScale
                      });
                    } else {
                      this.setState({
                      positionY: positions,
                      maxPositions: scaleValueY,
                      chartScale: cScale
                    });
                    }

                 }
                  }

                /*  updateLastValue(let value) {
                    this.setState({
                      lastValue: value
                    });
                  }*/


              render() {
                const strColor = 'blue';
                const frameColor = '#dce1e6';
                const guideColor = '#cd5d00';
                const fillColor = 'transparent';
                const fillWhiteColor = '#fff';
                const positioner = 'corrected';
                const layer1 = 'layer1';
                const layer2 = 'layer2';
                let horizontalLinePoint = parseInt(this.state.chartHeight * (1 / 8));
                let verticalLinePoint = parseInt(this.state.chartWidth * (1 / 8));
                let counterX = 2;
                let lines = inLines => {
                let innerS = [], xpos = 1,
                prevVal = 0;
                  for (let val of inLines) {
                    let valC = 210 + frameHeight;
                    //Автомасштаб - пропорциональное значение высоте поля - 10%
                    //if (xpos > 1) valC = (180 * (parseInt(this.state.positionY[xpos - 2]) / this.state.maxPositions)) + parseInt(startValue);
                    if (xpos > 1) valC = this.state.positionY[xpos - 2];
                    //if (xpos > 1) valC = this.state.lastValue;
                    //let valN = (180 * (parseInt(val)/ this.state.maxPositions)) + parseInt(startValue);
                    let valN = parseInt(val);
                    let xfirst = textFrame + ((xpos - 1) * chartSpeed);
                    innerS.push(<line x1={xfirst} y1={valC} x2={textFrame + (xpos * chartSpeed)} y2={valN} strokeWidth={1} stroke={strColor} key={xfirst + ' ' + strColor}/>);
                    //this.setState({
                    //  lastValue: valN
                    //});

                    xpos++;
                    //prevval = val;
                  }

                  return innerS;
                }
              return(
                 <svg className={positioner}>
                 <g>
                 <rect
                     fill = {fillWhiteColor}
                      stroke={frameColor}
                      strokeWidth={1}
                    //  strokeDasharray={'none'},
                    //  stroke-opacity={1}
                    width={chartsize + textFrame + textFrame}
                    height={480 + frameHeight}
                    x={0}
                    y={0}
                    />
                 </g>
                 <g id={layer2} z-index={500}>
                 <rect
                     fill = {fillColor}
                      stroke={frameColor}
                      strokeWidth={1}
                    //  strokeDasharray={'none'},
                    //  stroke-opacity={1}
                    width={this.state.chartWidth}
                    height={this.state.chartHeight}
                    x={textFrame}
                    y={frameHeight}
                    />
                    //Горизонтальная разметка
                    <line x1={textFrame} y1={(frameHeight + horizontalLinePoint)} x2={textFrame + chartsize} y2={(frameHeight + horizontalLinePoint)}
                    stroke={frameColor}
                    strokeWidth={1} />
                        <line x1={textFrame} y1={(frameHeight + parseInt(horizontalLinePoint * 2))} x2={textFrame + chartsize} y2={(frameHeight + parseInt(horizontalLinePoint * 2))}
                        stroke={frameColor}
                        strokeWidth={1} />
                        <line x1={textFrame} y1={(frameHeight + parseInt(horizontalLinePoint * 3))} x2={textFrame + chartsize} y2={(frameHeight + parseInt(horizontalLinePoint * 3))}
                      stroke={frameColor}
                      strokeWidth={1} />
                      <line x1={textFrame} y1={(frameHeight + parseInt(horizontalLinePoint * 4))} x2={textFrame + chartsize} y2={(frameHeight + parseInt(horizontalLinePoint * 4))}
                      stroke={frameColor}
                      strokeWidth={1} />
                      <line x1={textFrame} y1={(frameHeight + parseInt(horizontalLinePoint * 5))} x2={textFrame + chartsize} y2={(frameHeight + parseInt(horizontalLinePoint * 5))}
                    stroke={frameColor}
                    strokeWidth={1} />
                    <line x1={textFrame} y1={(frameHeight + parseInt(horizontalLinePoint * 6))} x2={textFrame + chartsize} y2={(frameHeight + parseInt(horizontalLinePoint * 6))}
                    stroke={frameColor}
                    strokeWidth={1} />
                        <line x1={textFrame} y1={(frameHeight + parseInt(horizontalLinePoint * 7))} x2={textFrame + chartsize} y2={(frameHeight + parseInt(horizontalLinePoint * 7))}
                        stroke={frameColor}
                        strokeWidth={1} />

                        <line x1={(textFrame + verticalLinePoint)} y1={frameHeight} x2={(textFrame + verticalLinePoint)} y2={frameHeight + this.state.chartHeight}
                        stroke={frameColor}
                        strokeWidth={1} />
                            <line x1={(textFrame + parseInt(verticalLinePoint*2))} y1={frameHeight} x2={(textFrame + parseInt(verticalLinePoint*2))} y2={frameHeight + this.state.chartHeight}
                            stroke={frameColor}
                            strokeWidth={1} />
                            <line x1={(textFrame + parseInt(verticalLinePoint*3))} y1={frameHeight} x2={(textFrame + parseInt(verticalLinePoint*3))} y2={frameHeight + this.state.chartHeight}
                          stroke={frameColor}
                          strokeWidth={1} />
                          <line x1={(textFrame + parseInt(verticalLinePoint*4))} y1={frameHeight} x2={(textFrame + parseInt(verticalLinePoint*4))} y2={frameHeight + this.state.chartHeight}
                          stroke={frameColor}
                          strokeWidth={1} />
                          <line x1={(textFrame + parseInt(verticalLinePoint*5))} y1={frameHeight} x2={(textFrame + parseInt(verticalLinePoint*5))} y2={frameHeight + this.state.chartHeight}
                        stroke={frameColor}
                        strokeWidth={1} />
                        <line x1={(textFrame + parseInt(verticalLinePoint*6))} y1={frameHeight} x2={(textFrame + parseInt(verticalLinePoint*6))} y2={frameHeight + this.state.chartHeight}
                        stroke={frameColor}
                        strokeWidth={1} />
                            <line x1={(textFrame + parseInt(verticalLinePoint*7))} y1={frameHeight} x2={(textFrame + parseInt(verticalLinePoint*7))} y2={frameHeight + this.state.chartHeight}
                            stroke={frameColor}
                            strokeWidth={1} />

                        //Вертикальная разметка
                          //Максимальное и минимальное значение
                            <text
                            fontSize={16}
                            line-height={1.25}
                            fill={frameColor}
                            stroke={frameColor}
                               x={textFrame / 2}
                               y={(frameHeight + parseInt(horizontalLinePoint * 4)) + 8}
                               ><tspan
                                 x={textFrame / 2}
                                 y={(frameHeight + parseInt(horizontalLinePoint * 4)) + 8}>0
                                 </tspan></text>

                                 <text
                                 fontSize={16}
                                 line-height={1.25}
                                 fill={frameColor}
                                 stroke={frameColor}
                                x={textFrame / 3}
                                y={frameHeight + 8}
                                ><tspan
                                x={textFrame / 3}
                                y={frameHeight + 8}>{this.state.maxPositions * (-1)}
                                </tspan></text>

                                <text
                                fontSize={16}
                                line-height={1.25}
                                fill={frameColor}
                                stroke={frameColor}
                               x={textFrame / 3}
                               y={(frameHeight + parseInt(horizontalLinePoint)*2) + 8}
                               ><tspan
                               x={textFrame / 3}
                               y={(frameHeight + parseInt(horizontalLinePoint)*2) + 8}>{this.state.maxPositions * (-0.5)}
                               </tspan></text>


                               <text
                               fontSize={16}
                               line-height={1.25}
                               fill={frameColor}
                               stroke={frameColor}
                              x={textFrame / 3}
                              y={(frameHeight + parseInt(horizontalLinePoint)*6) + 8}
                              ><tspan
                              x={textFrame / 3}
                              y={(frameHeight + parseInt(horizontalLinePoint)*6) + 8}>{this.state.maxPositions * (0.5)}
                              </tspan></text>

                              <text
                              fontSize={16}
                              line-height={1.25}
                              fill={frameColor}
                              stroke={frameColor}
                             x={textFrame / 3}
                             y={(frameHeight + parseInt(horizontalLinePoint)*8) + 8}
                             ><tspan
                             x={textFrame / 3}
                             y={(frameHeight + parseInt(horizontalLinePoint)*8) + 8}>{this.state.maxPositions}
                             </tspan></text>




                         //Масштаб времени
                       <text
                       fontSize={16}
                      line-height={1.25}
                      fill={frameColor}
                      stroke={frameColor}
                            x={textFrame + 5}
                          y={16 + frameHeight + this.state.chartHeight - 4}
                          ><tspan
                          x={textFrame + 5}
                        y={16 + frameHeight + this.state.chartHeight - 4}>{this.state.chartScale }
                      </tspan></text>

                      <text
                      fontSize={16}
                     line-height={1.25}
                     fill={frameColor}
                     stroke={frameColor}
                           x={textFrame + (chartsize * 0.25) - 8}
                         y={16 + frameHeight + this.state.chartHeight}
                         ><tspan
                         x={textFrame + (chartsize * 0.25) - 8}
                       y={16 + frameHeight + this.state.chartHeight}>{(this.state.chartScale * 0.75)}
                     </tspan></text>

                     <text
                     fontSize={16}
                    line-height={1.25}
                    fill={frameColor}
                    stroke={frameColor}
                          x={textFrame + (chartsize * 0.5) - 8}
                        y={16 + frameHeight + this.state.chartHeight}
                        ><tspan
                        x={textFrame + (chartsize * 0.5)}
                      y={16 + frameHeight + this.state.chartHeight}>{(this.state.chartScale * 0.5)}
                    </tspan></text>
                    <text
                    fontSize={16}
                   line-height={1.25}
                   fill={frameColor}
                   stroke={frameColor}
                         x={textFrame + (chartsize * 0.75) - 8}
                       y={16 + frameHeight + this.state.chartHeight}
                       ><tspan
                       x={textFrame + (chartsize * 0.75) - 8}
                     y={16 + frameHeight + this.state.chartHeight}>{(this.state.chartScale * 0.25)}
                   </tspan></text>

                   <text
                   fontSize={16}
                  line-height={1.25}
                  fill={frameColor}
                  stroke={frameColor}
                        x={textFrame + (chartsize) - 4}
                      y={16 + frameHeight + this.state.chartHeight}
                      ><tspan
                      x={textFrame + (chartsize) - 4}
                    y={16 + frameHeight + this.state.chartHeight}> 0
                  </tspan></text>
                 </g>
                 <g id={layer1}  z-index={900}>
                 {lines(this.state.positionY)}
                 </g>

                 </svg>
              );
              }
            }


</script>

</head>


<body>

<div class="navbar align-middle">
<div class="navbar-page-btn">
<a>График за период 100 Гц</a>
</div>
<div class="navbar-buttons align-middle">
  <a>Масштаб графика</a>
  <input id="scaleY" class="navbar-button btn-field" type="number" onchange="ScaleChartY ()" value="200" ></input><br>
  <a>Период отображения, с</a>

  <select name="Масштаб" class="navbar-button btn-field" id="scaleF" onchange="Scaler ()">
  <option selected>12</option>
  <option>3</option>
  <option>6</option>
  <option>24</option>
  <option>36</option>
  <option>60</option>
  </select><br>
  <button id = "pButton" class="navbar-button" onclick="Pause ()">Остановить график</button><br>
</div>
</div>


<div class="periodchart">
<div id="linechart"></div>
</div>
<div class="periodchart">
<div id="linechart2"></div>
</div>
</body>
<script>

</script>



</html>
