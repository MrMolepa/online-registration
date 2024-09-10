$(document).ready(function () {


	function lsTest() {
	  var test = "test";
	  try {
		localStorage.setItem(test, test);
		localStorage.removeItem(test);
		return true;
	  } catch (e) {
		return false;
	  }
	}
  
	// listen to storage event
	window.addEventListener("storage",
	  function (event) {
		// do what you want on logout-event
		if (event.key == "logout-event") {
		  window.location = "logout.php";
		}
	  },
	  false
	);
  
	if (lsTest()) {
	  // change logout-event and therefore send an event
	  localStorage.setItem("logout-event", "logout" + Math.random());
	  return true;
	} else {
	  // setInterval or setTimeout
	}
});  