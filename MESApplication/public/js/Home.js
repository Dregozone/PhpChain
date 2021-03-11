

function searchSn() {
    
    let sn = document.getElementById("sn").value;
    let user = document.getElementById("user").value;
    
    //alert("Searching for SN: " + sn);
    
    document.getElementById("snResults").innerText = "Searching for SN...";
    $("#snResults").load("../BlockchainDecentralisation/commands/viewSn.php?user=" + user + "&sn=" + sn); 
}

function addTransaction() {
    
    let snAdd = document.getElementById("sn").value;
    let user = document.getElementById("user").value;
    let action = document.getElementById("transactionAdd").value;
    
    //alert("Adding transaction " + action + " against SN: " + snAdd + " as user " + user);
    
    document.getElementById("addTransaction").innerText = "Preparing transaction...";
    $("#addTransaction").load("../BlockchainDecentralisation/commands/addTransaction.php?user=" + user + "&sn=" + snAdd + "&action=" + action); 
}
