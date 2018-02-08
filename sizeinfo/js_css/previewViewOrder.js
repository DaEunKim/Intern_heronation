$(document).ready(function(){

    // 미리보기 세로 div draggable
    $('.sortable_item').draggable({
        start : function(event, ui){
            var target = document.getElementById(this.id);
            target.style.zindex=100;
        },
        stop : function(event, ui){
            var nowPosition = new Object();
            var newPosition = new Array();

            for (var iter = 1; iter < 6; iter++) {
                var positionData = getTopPosition(iter);
                nowPosition = {'name':'item'+iter,'position':positionData};
                newPosition.push(nowPosition);
            }

            newPosition.sort(function(a,b){
                if( a['position'] > b['position'] ) return -1;
                if( a['position'] < b['position'] ) return 1;
                return 0;
            });

            var orderNumber = 1;
            for (var iter = newPosition.length; iter--; ) {
                // console.log(newPosition[iter].name);

                var tmpItem = document.getElementsByName(newPosition[iter].name);
                tmpItem[0].style.order = orderNumber;
                tmpItem[0].style.left = 0;
                tmpItem[0].style.top = 0;

                orderNumber++;
            }

            var target = document.getElementById(this.id);
            target.style.zindex=0;
        }
    });

    // 미리보기 가로 div(소재정보, 치수이미지) draggable
    $('.inline_sortable_item').draggable({
        start : function(event, ui){
            var target = document.getElementById(this.id);
            target.style.zindex=100;
        },
        stop : function(event, ui){
            var nowPosition = new Object();
            var newPosition = new Array();

            for (var iter = 1; iter < 3; iter++) {
                var positionData = getLeftPosition(iter);
                nowPosition = {'name':'inline_item'+iter,'position':positionData};

                newPosition.push(nowPosition);
            }

            newPosition.sort(function(a,b){
                if( a['position'] > b['position'] ) return -1;
                if( a['position'] < b['position'] ) return 1;
                return 0;
            });

            var orderNumber = 1;
            for (var iter = newPosition.length; iter--; ) {
                // console.log(newPosition[iter].name);

                var tmpItem = document.getElementsByName(newPosition[iter].name);
                tmpItem[0].style.order = orderNumber;
                tmpItem[0].style.left = 0;
                tmpItem[0].style.top = 0;

                orderNumber++;
            }

            var target = document.getElementById(this.id);
            target.style.zindex=0;
        }
    });
});

function getTopPosition(item){
    var tmpItem = document.getElementsByName('item'+item);
    return tmpItem[0].offsetTop;
}
function getLeftPosition(item){
    var tmpItem = document.getElementsByName('inline_item'+item);
    return tmpItem[0].offsetLeft;
}
