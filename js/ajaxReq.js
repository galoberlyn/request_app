function sendReqService(firstPartStat) {
	var xhttp = new XMLHttpRequest();
	var numOfServ = document.getElementsByName('description');
	var error = false;

 xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          document.getElementById("putHere").innerHTML = this.responseText;
          eval(document.getElementById("yeah").innerHTML);
       }
    };

var temp = location.href;
var num = temp.indexOf("?");
var requestId = temp.substring(num+1);


	for(var index = 0; index < numOfServ.length; index++){
		if(document.getElementById("yeah").textContent === 'sendReqService("Error")' || document.getElementById("yeah").textContent === "" || document.getElementById("yeah").textContent === 'sendReqService("noError")'){
			
			var desc = document.getElementById("descri"+index).value.trim();
			var sProvider = document.getElementById("serviceprov"+index).value.trim();
			var stat = document.getElementById("status"+index).value.trim();
			var dateCompleted = document.getElementById("dateComple"+index).value.trim();
			var remark = document.getElementById("remarks"+index).value.trim();
			var save = document.getElementById("save"+index).textContent;

			xhttp.open("POST", "editServiceInput.php", false);
			xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		  	xhttp.send("currIndex="+index+"&totalIndex="+numOfServ.length+"&firstPartStat="+firstPartStat+"&save="+save+"&description="+desc+"&serviceprovider="+sProvider+"&dateComp="+dateCompleted+"&status="+stat+"&remarks="+remark+"&"+requestId);
		
		}
		else{
			break;
		}
	}

}


function sendReqNotForPO(firstPartStat){

	var xhttp = new XMLHttpRequest();
	var numOfItemsNPO = document.getElementsByName('description');

	 xhttp.onreadystatechange = function() {
	        if (this.readyState == 4 && this.status == 200) {
	          document.getElementById("putHere").innerHTML = this.responseText;
	          eval(document.getElementById("yeah").innerHTML);
	       }
	    };

	var temp = location.href;
	var num = temp.indexOf("?");
	var requestId = temp.substring(num+1);

	for(var index = 0; index < numOfItemsNPO.length; index++){
		if(document.getElementById("yeah").textContent === 'sendReqNotForPO("Error")' || document.getElementById("yeah").textContent === "" || document.getElementById("yeah").textContent === 'sendReqNotForPO("noError")'){
			
			var desc = document.getElementById("descri"+index).value.trim();
			var quantity = document.getElementById("quantit"+index).value.trim();
			var supplier = document.getElementById("suppli"+index).value.trim();

			var dateAccomp = document.getElementById("dateAccompl"+index).value.trim();
			var amount = document.getElementById("amt"+index).value.trim();
			var stat = document.getElementById("status"+index).value.trim();
			
			var remark = document.getElementById("remarks"+index).value.trim();
			var save = document.getElementById("save"+index).textContent;


			xhttp.open("POST", "editNotForPOInput.php", false);
			xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhttp.send("currIndex="+index+"&totalIndex="+numOfItemsNPO.length+"&firstPartStat="+firstPartStat+"&"+requestId+"&save="+save+"&description="+desc+"&supplierNoPO="+supplier+"&quantity="+quantity+"&dateDel="+dateAccomp+"&amount="+amount+"&status="+stat+"&remarks="+remark);

		}
		else{
			break;
		}
	}
	location.reload();
}



function sendReqForPO(firstPartStat){

	var xhttp = new XMLHttpRequest();
	var numOfItemsPO = document.getElementsByName('description');

	 xhttp.onreadystatechange = function() {
	        if (this.readyState == 4 && this.status == 200) {
	          document.getElementById("putHere").innerHTML = this.responseText;
	          eval(document.getElementById("yeah").innerHTML);
	       }
	    };

	var temp = window.location.href;
	var num = temp.indexOf("?");
	var requestId = temp.substring(num+1);


	for(var index =0; index < numOfItemsPO.length; index++){

		if(document.getElementById("yeah").textContent === 'sendReqForPO("Error")' || document.getElementById("yeah").textContent === "" || document.getElementById("yeah").textContent === 'sendReqForPO("noError")'){
			var desc = document.getElementById("descri"+index).value.trim();
			var quantity = document.getElementById("quantit"+index).value.trim();
			var supplier = document.getElementById("suppli"+index).value.trim();

			var location = document.getElementById("loca"+index).value.trim();
			var uPrice = document.getElementById("unitPrice"+index).value.trim();
			var dateDeliv = document.getElementById("dateDeliv"+index).value.trim();
			var stat = document.getElementById("status"+index).value.trim();

			var remark = document.getElementById("remarks"+index).value.trim();

			var save = document.getElementById("save"+index).textContent;


			xhttp.open("POST", "editForPOInput.php", false);
			xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhttp.send("currIndex="+index+"&totalIndex="+numOfItemsPO.length+"&firstPartStat="+firstPartStat+"&"+requestId+"&save="+save+"&description="+desc+"&spo="+supplier+"&quantity="+quantity+"&location="+location+"&unitPrice="+uPrice+"&dateDeli="+dateDeliv+"&status="+stat+"&remarks="+remark);
		}
		else{
			break;
		}
	}
}

function sendfirstPartServices(rsn){
	var xhttp = new XMLHttpRequest();
	var dateNeeded = document.getElementById("dateEdit").value.trim();
	var timeNeeded = document.getElementById("timeEdit").value.trim();
	var purpose = document.getElementById("purposeEdit").value.trim();
	var status = document.getElementById("statusEdit").value;
	var careOF = document.getElementById("careEdit").value.trim();

	var temp = window.location.href;
	var num = temp.indexOf("?");
	var requestId = temp.substring(num+1);

	 xhttp.onreadystatechange = function() {
	        if (this.readyState == 4 && this.status == 200) {
	          document.getElementById("putHere").innerHTML = this.responseText;
	          eval(document.getElementById("yeah").innerHTML);
	       }
	    };
	 xhttp.open("POST", "editInput.php", true);
	 xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	 xhttp.send(requestId+"&dateNeeded="+dateNeeded+"&theTimeNeeded="+timeNeeded+"&purpose="+purpose+"&slipStatus="+status+"&careOF="+careOF+"&type=Service&rsn="+rsn);

}

function sendfirstPartNotForPo(rsn){
	var xhttp = new XMLHttpRequest();
	var dateNeeded = document.getElementById("dateEdit").value.trim();
	var timeNeeded = document.getElementById("timeEdit").value.trim();
	var purpose = document.getElementById("purposeEdit").value.trim();
	var status = document.getElementById("statusEdit").value;
	var careOF = document.getElementById("careEdit").value.trim();


	var temp = window.location.href;
	var num = temp.indexOf("?");
	var requestId = temp.substring(num+1);

	 xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          document.getElementById("putHere").innerHTML = this.responseText;
          eval(document.getElementById("yeah").innerHTML);
       }
    };
    xhttp.open("POST", "editInput.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(requestId+"&dateNeeded="+dateNeeded+"&theTimeNeeded="+timeNeeded+"&purpose="+purpose+"&slipStatus="+status+"&careOF="+careOF+"&type=ItemsNoPO&rsn="+rsn);

}


function sendfirstPartForPO(rsn){
	var xhttp = new XMLHttpRequest();

	var dateNeeded = document.getElementById("dateEdit").value.trim();
	var timeNeeded = document.getElementById("timeEdit").value.trim();
	var purpose = document.getElementById("purposeEdit").value.trim();
	var status = document.getElementById("statusEdit").value;
	var PONum = document.querySelector("input[name=poNum]").value.trim();
	var PODate = document.querySelector("input[name=poDate]").value.trim();	
	var supplier = document.querySelector("input[name=poSupp]").value.trim();

	var temp = window.location.href;
	var num = temp.indexOf("?");
	var requestId = temp.substring(num+1);	

	 xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          document.getElementById("putHere").innerHTML = this.responseText;
          eval(document.getElementById("yeah").innerHTML);
       }
    };

    xhttp.open("POST", "editInput.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(requestId+"&dateNeeded="+dateNeeded+"&theTimeNeeded="+timeNeeded+"&purpose="+purpose+"&slipStatus="+status+"&poNum="+PONum+"&poDate="+PODate+"&poSupp="+supplier+"&type=PO&rsn="+rsn);


}