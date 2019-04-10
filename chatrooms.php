<?php
session_start();

//Checks for ajax flag
isset($_POST['flag'])? $flag = $_POST['flag'] : $flag = "";

//lists the chatrooms
function listChatrooms(){
	$xml = simplexml_load_file("xml/chatrooms.xml");
	echo "<h2>Chatrooms</h2>";
	foreach($xml->children() as $chatroom) {
		echo '<div class="chatroom">
				<form>
					<input type="hidden" name="chatroomId" value="'. $chatroom->attributes()->chatroomId . '"/>' .
					"<input type='submit' class='chatroomName' name='chatroomName' value='$chatroom->chatroomName'/>
				</form>
				<p>$chatroom->description</p>
			   </div>";
	}
}

function createMessage($file){
	$xmldoc = new DOMDocument('1.0', 'utf-8');
	$xmldoc->preserveWhiteSpace = false;
	$xmldoc->formatOutput = true;

	$xmldoc->load($file);
	$root = $xmldoc->documentElement;

	//add new <message> element
	$message = $xmldoc->createElement("message");
	$messageId = $xmldoc->createAttribute("messageId");
	$messages = $xmldoc->getElementsByTagName("message");

	//loops through messages and finds max messageId value
	$max = 0;
    foreach($messages as $myMessage){
        $current = $myMessage->getAttribute('messageId');
        if($current>$max){
            $max = $current;
        }
    }
	//Value of new messageId is the max messageId value + 1
    $messageId->value = $max + 1;
    $message->appendChild($messageId);

	//add <username> to <message>
	$username = $xmldoc->createElement("username");
	$usernameText = $xmldoc->createTextNode($_SESSION["username"]);
	$username->appendChild($usernameText);
	$message->appendChild($username);

	//add <submissionTime> to <message>
	$submissionTime = $xmldoc->createElement("submissionTime");
	$dt = gmdate('Y-m-d\TH:i:s\Z');
	$submissionTimeText = $xmldoc->createTextNode($dt);
	$submissionTime->appendChild($submissionTimeText);
	$message->appendChild($submissionTime);

	//add <messageText> to <message>
	$messageText = $xmldoc->createElement("messageText");
	$messageTextNode = $xmldoc->createTextNode($_POST['newMessage']);
	$messageText->appendChild($messageTextNode);
	$message->appendChild($messageText);

	$root->appendChild($message);
	$xmldoc->save($file);
}

//prints the messages of the chatroom
function printMessages(){
	$chatroomId = $_POST['chatroomId'];
	$file = "xml/chatroom$chatroomId.xml";
	$xml = simplexml_load_file($file);

	foreach($xml->children() as $message) {
		echo "<div class='message'>
				<div class='submissionTime'>";

		$submissionTime = new DateTime($message->submissionTime);
		//displays message time in EST
		$submissionTime->setTimeZone(new DateTimeZone('Canada/Eastern'));
		echo $submissionTime->format('m/d/Y h:i A');

		echo "</div>
				<div class='username'>$message->username</div>
				<div class='messageText'>$message->messageText</div>
			 </div>";
	}
}

//If a username has been entered
	if($flag=="login"){
		$username = $_POST['username'];
		$password = $_POST['password'];

		//Check if the username entered exists
		$users = simplexml_load_file("xml/users.xml");
		$validUser = false;

		foreach($users->children() as $user) {
			//if stored username and password matches the entered username (not case sensitive) and password
			if(strcasecmp($user->username, $username)==0 && password_verify($password, $user->password)){
				$validUser = true;
				$_SESSION["username"] = (string)$user->username;
				break;
			}
		}

		//if username exists, displays list of chatrooms
		if($validUser==true){
			listChatrooms();
		}
		else{
			//if username doesn't exist, gives error
			echo "Invalid";
		}
	}

	//if user logs in with google sign in, sets the username as their google name
	else if($flag=="googleLogin"){
		$_SESSION['username'] = $_POST['username'];
		listChatrooms();
	}
	//Displays username and logout button if user is signed in
	else if($flag=="welcome"){
		$username = $_SESSION['username'];
		echo "<div id='userInfo'>
		   		Welcome $username! |
		   		<button id='logout'>Logout</button>
		   	 </div>";
	}
	//When ajax flag is "signOut", destroys the session
	else if($flag=="signOut"){
		session_destroy();
	}
	//if a chatroom has been selected
	else if(isset($_POST['chatroomId']) && $flag!='refresh'){
		$chatroomId = $_POST['chatroomId'];
		$chatroomName = $_POST['chatroomName'];

		$file = "xml/chatroom$chatroomId.xml";

		echo "<div id='chatroomWindow'><h2>$chatroomName</h2>";

		//if theres a new message add it to file
		if(isset($_POST['newMessage'])){
			createMessage($file);
		}

		echo '<div id="messages">';
		//prints out messages from chatroom
		printMessages();
		echo '</div>'; //end of messages
		echo '</div>'; //end of chatroom window

		//new message box
		echo "<div>
				<form id='newMessageForm'>
					<input type='hidden' id='chatroomId' name='chatroomId' value='$chatroomId' />
					<input type='hidden' id='chatroomName' name='chatroomName' value='$chatroomName' />
					<div id='newMessageContainer' class='flex-container'>
						<label for='newMessage' class='hidden'>New Message</label>
						<input type='text' id='newMessage' name='newMessage' placeholder='Type a message...'/>
						<input id='send' type='submit' value='Send'>
					</div>
				</form>
			 </div>
			 <button id='back' class='button' value='Back'>Back to Chatroom List</button>";
		 }

	//when the ajax flag is "refresh", refreshes the chatroom messages
    else if($flag=='refresh'){
		printMessages();
	}
	//when the ajax flag is "list", displays a lists the chatrooms
	else if($flag=="list"){
		listChatrooms();
	}
?>
