//Google sign in
//Referenced https://developers.google.com/identity/sign-in/web/sign-in
function onSignIn(googleUser) {
	//hides login form
	$("#loginForm").hide();
	let profile = googleUser.getBasicProfile();
	let username = profile.getName();
	$.post(
	  "chatrooms.php", {
		  flag: "googleLogin",
		  username: username
	  },
	  function(result) {
		  $("#view").html(result);
		  welcomeUser();
	  }
  )
}

function signOut() {
  //signs out of google account
  var auth2 = gapi.auth2.getAuthInstance();
  auth2.signOut().then(function (){
	console.log('User signed out.');
  });

  $.post(
	 //to clear session variable
	"chatrooms.php", {
		flag: "signOut",
	},
	function(result) {
		//clears menu
		$("#userMenu").html("");
		//clears view
		$("#view").html("");
		//shows login form
		$("#loginForm").show();
	});
}

function welcomeUser(){
	$.post(
		"chatrooms.php", {
			flag: "welcome"
		},
		function(result) {
			$("#userMenu").html(result);
		}
	)
}

let messageRefresh; //variable that will be used to set interval for refreshing chatroom messages

function listChatrooms(){
	//Stops the interval that refreshes the chatroom messages
	clearInterval(messageRefresh);
	$.post(
		"chatrooms.php", {
			flag: "list"
		},
		function(result) {
			$("#view").html(result);
		}
	)
}

$(document).ready(function(){
	$( "#userMenu" ).on("click", "#logout", function(){
		signOut();
	});

	$( "#main-header-logo" ).click(function(){
		listChatrooms();
	});

	//When the user logs in
	$("#login").submit(function(e) {
	    e.preventDefault();
	    let username = $("#username").val();
	    let password = $("#password").val();
	    $.post(
	        "chatrooms.php", {
				flag: "login",
	            username: username,
				password: password
	        },
	        function(result) {
	            if (result == "Invalid") {
	                $("#error").html("Invalid username or password. Please try again.");
	            }
				else {
				//hides login form
					$("#loginForm").hide();
				//display chatlist
	                $("#view").html(result);
					welcomeUser();
	            }
	        }
	    );
	});

	//When the user clicks on a chatroom/chatroom title
	$("#view").on("click", ".chatroom", function(e){
		e.preventDefault();
	    let chatroomId = $(this).find("input[name='chatroomId']").val();
	    let chatroomName = $(this).find("input[name='chatroomName']").val();
	    $.post(
	        "chatrooms.php", {
	            chatroomId: chatroomId,
	            chatroomName: chatroomName
	        },
	        function(result) {
	            $("#view").html(result);
	        }
	    );

		//Periodically refreshes messages in the chatroom
		messageRefresh = setInterval(function(){
			$.post(
				"chatrooms.php", {
					flag: "refresh",
					chatroomId: chatroomId,
			        chatroomName: chatroomName
				},
				function(result) {
				   $("#messages").html(result);
			   });
		}, 1000);
	});

	//New message
	$("#view").on("submit", "#newMessageForm", function(e) {
	    e.preventDefault();
	    let chatroomId = $("#chatroomId").val();
	    let chatroomName = $("#chatroomName").val();
	    let newMessage = $("#newMessage").val();
	    $.post(
	        "chatrooms.php", {
	            chatroomId: chatroomId,
	            chatroomName: chatroomName,
	            newMessage: newMessage
	        },
	        function(result) {
	            $("#view").html(result);
	        }
	    );
	});

	//Back button is clicked
	$("#view").on("click", "#back", function() {
		listChatrooms();
	});

}); //end of document ready
