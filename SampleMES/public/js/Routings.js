
function addOperation(sequence, routingName) {
    
    let opName = document.getElementById("opName" + sequence).value;
    
    //alert("Add operation: " + opName + " before Sequence: " + sequence + " on routing " + routingName);
    
    window.location.replace("?p=Routings&action=addOperation&routing=" + routingName + "&operation=" + opName + "&sequence=" + sequence);
}
