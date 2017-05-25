<?php
/**
 * @copyright	Copyright ? 2014 - All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @author		Joompolitan -> Envolute
 * @author mail	dev@envolute.com
 * @website		http://www.envolute.com
 */
 
defined('_JEXEC') or die('Restricted access');

$db = JFactory::getDbo();
$app = JFactory::getApplication();

// get lest message ID
$query = "SELECT MAX(message_id) FROM #__simplechatsupport_message";
$db->setQuery($query);
$lastMessageAll = $db->loadResult();	

// imagens
$loader = JURI::base()."components/com_simplechatsupport/images/loader.gif";
$smile1 = JURI::base()."components/com_simplechatsupport/images/smile/smile.png";
$smile2 = JURI::base()."components/com_simplechatsupport/images/smile/angry.png";
$smile3 = JURI::base()."components/com_simplechatsupport/images/smile/lol.png";
$smile4 = JURI::base()."components/com_simplechatsupport/images/smile/sad.png";
$smile5 = JURI::base()."components/com_simplechatsupport/images/smile/surprised.png";
$smile6 = JURI::base()."components/com_simplechatsupport/images/smile/wink.png";
?>
<style type="text/css" media="screen">
	
	table {
		width: 100%;
	}
	
	legend {
		font-size: 16px;
		margin-bottom: 0px;				
	}
	
	form {
		margin: 0;
	}

	#div_rooms {
		height: 215px; 
		overflow: auto;
		background-color: #fff; 
		border-bottom: 2px solid #ddd;			
	} 
	#div_saved_rooms {
		height: 150px; 
		overflow: auto;
		background-color: #fff; 
		border-bottom: 2px solid #ddd;				
	} 
	.chat-list {			  	
		padding-top: 5px;
		overflow: auto;
		background-color: #fff;	
	}
	.chat-list a {			  	
		display: block;
		line-height: 22px;
		text-decoration: none;		
		border-bottom: 1px dashed #ddd;	
	}  
	.chat-list a:hover {
		background: #f8f8f8;	
	}
	.chat-list a.active:before {			  	
		content: '\00BB\0020';			
	}
	.chat-list a.active {
		font-weight: bold;
		color: #fa0;				
	}
	.chat-list a img {
		vertical-align: sub;
	} 
	.chat-list a.queued {
		font-weight: bold;
		color: #f00;				
	}
	#div_chat {			
		height: 200px; 
		padding: 10px;
		margin-bottom: 10px;
		font-size: 12px;
		overflow: auto; 
		background-color: #ffe; 
		border: 1px solid #ddd;
		border-top: none;
		-webkit-border-radius: 0 0 4px 4px;
			border-radius: 0 0 4px 4px;
	}
	.chat_time {
		float: right;
		font-size: 10px;
		color: #f80;
	}
	#btn_send_chat {
		width: 16%;
		height: 58px;
		float: right;
		font-weight: bold;
	}
	.input-append {
		margin: 5px 0 0;
	}		
</style>		
				 
	<script language="JavaScript" type="text/javascript">
	var statusReq = getXmlHttpRequestObject();
	var resetReq = getXmlHttpRequestObject();
	var sendReq = getXmlHttpRequestObject();
	var receiveReq = getXmlHttpRequestObject();
	var receiveReqRoom = getXmlHttpRequestObject();
	var lastMessage = 0;
	var lastMessageAll = <?php echo ($lastMessageAll) ? $lastMessageAll : 0; ?>;
	var lastRoom = 0;			
	var id;
	var mTimer;
	var mTimerRooms;
	var status = 0;
	var online = 0;
	var start = 0;
	var chat_reset = 0;
	
	var predefinedMsg = new Array();
	<?php
		for($i=0; $i < count( $this->template_messages ); $i++) {
			$row = $this->template_messages[$i];
	?>
		predefinedMsg[<?php echo $i; ?>] = "<?php echo str_replace(array("\r\n", "\r", "\n"), "\\n", addslashes($row->message));  ?>";	
	<?php } ?>	

	//Function for initializating the page.
	var curID = 0;
	function startChat(room_id, chat) {
		//Clear message counter
		lastMessage = 0;
		if(chat == null) chat = false; //saved false = active

		//Clear out the existing timer so we don't have 
		//multiple timer instances running.
		clearInterval(mTimer);
		
		//Set the focus to the Message Box.
		if(chat) document.getElementById('txt_message').focus();
		document.getElementById('txt_message').disabled =  chat;
		document.getElementById('div_chat').innerHTML = '';
		
		//select current room
		if(room_id) {
			if(curID != 0) document.getElementById('room_'+curID).className = '';
			document.getElementById('room_'+room_id).className = 'active';
			id = curID = room_id;
		}
		//Start Recieving Messages.
		getChatText();
	}		
	//Gets the browser specific XmlHttpRequest Object
	function getXmlHttpRequestObject() {
		if (window.XMLHttpRequest) {
			return new XMLHttpRequest();
		} else if(window.ActiveXObject) {
			return new ActiveXObject("Microsoft.XMLHTTP");
		} else {
			document.getElementById('p_status').innerHTML = 'Status: Cound not create XmlHttpRequest Object.  Consider upgrading your browser.';
		}
	}

	//set on/off chat status
	function setStatus(sts) {
		status = sts;
		if (statusReq.readyState == 4 || sendReq.readyState == 0) {
			statusReq.open("POST", 'components/com_simplechatsupport/statusChat.php?status=' + status , true);
			statusReq.onreadystatechange = handleStatusChat;
			statusReq.send(null);
		}		
	}
	//Gets the current messages from the server
	function getChatText() {
		if (receiveReq.readyState == 4 || receiveReq.readyState == 0) {
			receiveReq.open("POST", 'components/com_simplechatsupport/getChat.php?chat=' + id + '&last=' + lastMessage + '&lastAll=' + lastMessageAll, true);
			receiveReq.onreadystatechange = handleReceiveChat; 
			receiveReq.send(null);
		}		
	}
	//Add a message to the chat server.
	function sendChatText() {
		if(document.getElementById('txt_name').value == '') {
			alert("Por favor, informe seu nome!");
			return;
		}
		if(document.getElementById('txt_message').value == '') {
			alert("Você não digitou a mensagem");
			return;
		}
		if(!id) {
			alert("Nenhuma conversa foi iniciada!");
			return;
		}
		if (receiveReq.readyState == 4 || receiveReq.readyState == 0) {
			receiveReq.open("POST", 'components/com_simplechatsupport/getChat.php?chat=' + id + '&last=' + lastMessage, true);
			receiveReq.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
			receiveReq.onreadystatechange = handleSendChat; 
			var param = 'message=' + document.getElementById('txt_message').value;
			param += '&name=' + document.getElementById('txt_name').value;
			param += '&chat=' + id;
			receiveReq.send(param);
			document.getElementById('txt_message').value = '';
		}							
	}
	
	//When our status is set.
	function handleStatusChat() {
		if (statusReq.readyState == 4) {
			var sts = statusReq.responseText;
			if(sts == 'Off line') {
				document.getElementById('alert-status').className = 'alert alert-danger';
				document.getElementById('btn-status-on').style.display = '';
				document.getElementById('btn-status-off').style.display = 'none';
				online = 0;
			} else {
				document.getElementById('alert-status').className = 'alert alert-success';
				document.getElementById('btn-status-on').style.display = 'none';
				document.getElementById('btn-status-off').style.display = '';
				online = 1;
			}
			document.getElementById('set-status').innerHTML = sts;
			resetChat();
		}
	}			
	
	//When our message has been sent, update our page.
	function handleSendChat() {
		//Clear out the existing timer so we don't have 
		//multiple timer instances running.
		clearInterval(mTimer);
		getChatText();
	}			

	//Function for handling the return of chat text
	var count = 0;
	function handleReceiveChat() {
		if (receiveReq.readyState == 4) {
			
			var chat_div = document.getElementById('div_chat');
			var xmldoc = receiveReq.responseXML;
			
			//Listen current chat
			var message_nodes = xmldoc.getElementsByTagName("message");
			var n_messages = message_nodes.length;
			for (i = 0; i < n_messages; i++) {
				var user_node = message_nodes[i].getElementsByTagName("user");
				var text_node = message_nodes[i].getElementsByTagName("text");
				var time_node = message_nodes[i].getElementsByTagName("time");
				chat_div.innerHTML += '<p><strong class="text-live">' + user_node[0].firstChild.nodeValue + '</strong> - <em class="small tam2 text-live">' + time_node[0].firstChild.nodeValue + '</em><br /><span>' + text_node[0].firstChild.nodeValue + '</span></p>';
				
				chat_div.innerHTML = chat_div.innerHTML.replace(':)', '<img src=\'<?php echo $smile1;?>\'>');
				chat_div.innerHTML = chat_div.innerHTML.replace(':@', '<img src=\'<?php echo $smile2;?>\'>');
				chat_div.innerHTML = chat_div.innerHTML.replace(':d', '<img src=\'<?php echo $smile3;?>\'>');
				chat_div.innerHTML = chat_div.innerHTML.replace(':(', '<img src=\'<?php echo $smile4;?>\'>');
				chat_div.innerHTML = chat_div.innerHTML.replace(':o', '<img src=\'<?php echo $smile5;?>\'>');	
				chat_div.innerHTML = chat_div.innerHTML.replace(';)', '<img src=\'<?php echo $smile6;?>\'>');

				document.getElementById('soundChat').play();
				chat_div.scrollTop = chat_div.scrollHeight;
				lastMessage = (message_nodes[i].getAttribute('id'));
				document.getElementById('txt_message').focus();
			}
			
			//Listen all chats
			var messages_nodes = xmldoc.getElementsByTagName("messages");
			var n_messagesAll = messages_nodes.length;
			for (i = 0; i < n_messagesAll; i++) {
				var room_node = messages_nodes[i].getElementsByTagName("room");
				if(document.getElementById('room_'+room_node[0].firstChild.nodeValue).className !== 'active') {
					document.getElementById('room_'+room_node[0].firstChild.nodeValue).className = 'queued';
					document.getElementById('soundChat').play();
				}
				lastMessageAll = lastMessageAll + 1;
			}
			
			mTimer = setTimeout('getChatText();',5000); //Refresh our chat in 5 seconds
		}
	}

	//This functions handles when the user presses enter.  Instead of submitting the form, we
	//send a new message to the server and return false.
	function blockSubmit() {
		sendChatText();
		return false;
	}


	//This cleans out the database so we can start a new chat session.
	function resetChat() {
		if (sendReq.readyState == 4 || sendReq.readyState == 0) {
			sendReq.open("POST", 'components/com_simplechatsupport/getChat.php?chat=' + id + '&last=' + lastMessage, true);
			sendReq.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
			sendReq.onreadystatechange = handleResetChat; 
			var param = 'action=reset';
			sendReq.send(param);
			document.getElementById('txt_message').value = '';
			var curr = document.getElementById('room_'+curID);
			if(curID != 0 && curr !== undefined && curr !== null) curr.className = '';
			id = curID = start = 0;
		}							
	}		

	//This saves the chat to another table, so we can review it on a later time.
	function saveChat() {
		if (sendReq.readyState == 4 || sendReq.readyState == 0) {
			sendReq.open("POST", 'components/com_simplechatsupport/getChatRoom.php?chat=' + id + '&last=' + lastRoom, true);
			sendReq.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
			var param = 'action=save';
			sendReq.send(param);
			document.getElementById('txt_message').value = '';
			document.getElementById('div_chat').innerHTML = '<?php echo jtext::_('COM_SIMPLECHATSUPPORT_SAVED');?>';
			document.getElementById('room_'+id).style.background = '#F0F9EC';
			div_saved_rooms
		}							
	}
	
	function chatReset(reload) {
		chat_reset = 1;
		if(reload) window.location.href = '<?php echo JURI::base(); ?>index.php?option=com_simplechatsupport';
	}			

	//This cleans out the database so we can start a new chat session.
	function resetChatRoom() {
		if (confirm('<?php echo jtext::_('COM_SIMPLECHATSUPPORT_DELETE_ALERT')?>')) {
			if (resetReq.readyState == 4 || resetReq.readyState == 0) {
				resetReq.open("POST", 'components/com_simplechatsupport/getChatRoom.php?chat=' + id + '&last=' + lastRoom, true);
				resetReq.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
				resetReq.onreadystatechange = handleResetChatRoom; 
				var param = 'action=reset';
				resetReq.send(param);
				document.getElementById('txt_message').value = '';
				var curr = document.getElementById('room_'+curID);
				if(curID != 0 && curr !== undefined && curr !== null) curr.style.display = 'none';
				id = curID = start = 0;
			}
		}							
	}
	function resetAllChatRoom() {
		if (confirm('<?php echo jtext::_('COM_SIMPLECHATSUPPORT_DELETE_ALL_ALERT')?>')) {
			if (resetReq.readyState == 4 || resetReq.readyState == 0) {
				resetReq.open("POST", 'components/com_simplechatsupport/getChatRoom.php?chat=' + id, true);
				resetReq.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
				resetReq.onreadystatechange = handleResetChatRoom; 
				var param = 'action=resetAll';
				resetReq.send(param);
				document.getElementById('txt_message').value = '';
				id = curID = start = 0;
			}
		}
	}

	//This function handles the response after the page has been refreshed.
	function handleResetChat() {
		document.getElementById('div_chat').innerHTML = '';
		getChatText();
	}	

	//This function handles the response after the page has been refreshed.
	function handleResetChatRoom() {
		document.getElementById('div_chat').innerHTML = '';
		document.getElementById('div_rooms').innerHTML = '';
		lastRoom = 0;
		getChatText();
	}	

	//Gets the current chat rooms from the server
	function getChatRoom() {
		if (receiveReqRoom.readyState == 4 || receiveReqRoom.readyState == 0) {
			receiveReqRoom.open("GET", 'components/com_simplechatsupport/getChatRoom.php?t=' + Math.random() + '&last=' + lastRoom, true);
			receiveReqRoom.onreadystatechange = handleChatRooms; 
			receiveReqRoom.send(null); 
		}		
	}

	//Function for handling the chat rooms
	function handleChatRooms() {
		if (receiveReqRoom.readyState == 4) {
			var room_div = document.getElementById('div_rooms');
			var xmldoc = receiveReqRoom.responseXML;
			
			if(xmldoc){
				var message_nodes = xmldoc.getElementsByTagName("chat"); 
				var n_messages = message_nodes.length;
				if(n_messages == 0) start = 1;
				for (i = 0; i < n_messages; i++) {
					var name_node = message_nodes[i].getElementsByTagName("name");
					var time_node = message_nodes[i].getElementsByTagName("time");
					var time = '<em class="chat_time">' + time_node[0].firstChild.nodeValue + '</em>';
					if(n_messages <= 1 && start == 1){
						document.getElementById('soundNotification').play();
						room_div.innerHTML += '<a id="room_' + message_nodes[i].getAttribute('id') + '" class="queued" href=\"javascript:startChat(' + message_nodes[i].getAttribute('id') + ');\">' + name_node[0].firstChild.nodeValue + ' ' + time + '</a>';
						
					} else {
						room_div.innerHTML += '<a id="room_' + message_nodes[i].getAttribute('id') + '" href=\"javascript:startChat(' + message_nodes[i].getAttribute('id') + ');\">' + name_node[0].firstChild.nodeValue + ' ' + time + '</a>';					
						
					}
					room_div.scrollTop = room_div.scrollHeight;	
					lastRoom = (message_nodes[i].getAttribute('id'));
				}
			}
			
			if(mTimerRooms != null) {
				clearTimeout(mTimerRooms);
			}
			mTimerRooms = setTimeout('getChatRoom();',5000); //Refresh our chat in 5 s\econds
		}
		
	}

	function smile(code){				
		this.code = code;
		document.getElementById('txt_message').value += code
	}
	
	function setTemplateMessage(id){				
		document.getElementById('txt_message').value = predefinedMsg[id]
		document.getElementById('txt_message').focus();
	}	

	function refreshChatRoom() {
		getChatRoom();
	}
	</script>
	
	<h2 id="alert-status" style="margin-bottom: 10px;">
		Status: <span id="set-status" style="display:inline-block; width:100px"></span>
		<button id="btn-status-on" class="btn btn-success" onclick="setStatus(1)"><?php echo jtext::_('COM_SIMPLECHATSUPPORT_START_CHAT');?></button>
		<button id="btn-status-off" class="btn btn-danger" onclick="setStatus(0)"><?php echo jtext::_('COM_SIMPLECHATSUPPORT_CLOSE_CHAT');?></button>
		<input type="button" class="btn btn-warning" name="btn_reset_chat" id="btn_reset_chat" value="<?php echo jtext::_('COM_SIMPLECHATSUPPORT_RESET');?>" onclick="chatReset(true);" />
	</h3>		
  
	<table align="left">
		<tr>
			<td style="width: 220px;" valign="top">		  
				<fieldset>
					<legend><?php echo jtext::_('COM_SIMPLECHATSUPPORT_USERS');?></legend>
					<div id="div_rooms" class="chat-list"></div>
				</fieldset>
				
				<form name="filterer" action="" method="get" onsubmit="chatReset(false)">	  
					<fieldset>
						<legend>
							<?php echo jtext::_('COM_SIMPLECHATSUPPORT_USERS_SAVED'); ?>
							<?php if($lastMessageAll) : ?>
							<select name="period" style="width:auto; margin-top:4px; float:right;" onchange="chatReset(false); document.filterer.submit()">
								<?php
								for($i=0; $i < count( $this->saved_months ); $i++) {
									$row = $this->saved_months[$i];
									$dt = explode('-', $row->date);
									$sel = ($row->date == $this->period) ? ' selected="selected"' : '';
									echo '<option value="'.$row->date.'"'.$sel.'>'.jtext::_('COM_SIMPLECHATSUPPORT_FILTER_MONTH_'.$dt[0]).$dt[1].'</option>';
								}
								?>
							</select>
							<? endif; ?>
						</legend>
						<div class="input-prepend input-append">
							<span class="add-on"><span class="icon-filter"></span></span>
							<input type="text" name="filter" id="filter" value="<?php echo $this->user; ?>" placeholder="<?php echo jtext::_('COM_SIMPLECHATSUPPORT_FILTER_SAVED');?>" />
							<input type="hidden" name="option" value="com_simplechatsupport" />
							<button class="btn" type="submit"><?php echo jtext::_('COM_SIMPLECHATSUPPORT_FILTER_SAVED_BTN');?></button>
							<?php if(!empty($this->filter)) : ?>
								<button class="btn btn-danger" type="button" onclick="chatReset(true)">x</button>
							<?php endif; ?>
						</div>
						<div id="div_saved_rooms" class="chat-list">
							<?php
							for($i=0; $i < count( $this->saved_rooms ); $i++) {
								$row = $this->saved_rooms[$i];
								$time = strtotime($row->start_time);
								$time = date("d.m.y H:i", $time);
								echo '<a id="room_'.$row->chat_id.'" href="javascript:startChat('.$row->chat_id.', true);">'.$row->chat_name.' <em class="chat_time">'.$time.'</em></a>';
							}
							?>
						</div>
					</fieldset>
				</form>
			</td>
			<td style="width: 15px;">&nbsp;</td>
			<td valign="top"> 
				<fieldset>
					<legend><?php echo jtext::_('COM_SIMPLECHATSUPPORT_CHAT');?></legend>				
					<div id="div_chat">			
					</div>
				</fieldset>
				<form id="frmmain" name="frmmain" onsubmit="return blockSubmit();">
					<p style="text-align: right">
						<span style="padding-bottom: 5px">						  
							<input type="button" class="btn btn-small" name="btn_save_chat" id="btn_save_chat" value="<?php echo jtext::_('COM_SIMPLECHATSUPPORT_SAVE');?>" onclick="javascript:saveChat();" />
							<input type="button" class="btn btn-small btn-danger" name="btn_reset_chat_room" id="btn_reset_chat_room" value="<?php echo jtext::_('COM_SIMPLECHATSUPPORT_DELETE');?>" onclick="javascript:resetChatRoom();" />
							<input type="button" class="btn btn-small btn-danger" name="btn_reset_all_chat_room" id="btn_reset_all_chat_room" value="<?php echo jtext::_('COM_SIMPLECHATSUPPORT_DELETE_ALL');?>" onclick="javascript:resetAllChatRoom();" />
						</span>			
						<span style="float:left">
							<?php $user = JFactory::getUser(); ?>
							<input type="text" id="txt_name" name="txt_name" value="<?php echo $user->name ?>"/> (<strong><?php echo jtext::_('COM_SIMPLECHATSUPPORT_OPERATOR');?></strong>)
						</span>
					</p>
					<p>
						<label><?php echo jtext::_('COM_SIMPLECHATSUPPORT_MESSAGE');?></label>
						<textarea id="txt_message" name="txt_message" style="width: 80%; height: 50px;"></textarea>
						<input type="button" name="btn_send_chat" id="btn_send_chat" class="btn btn-primary" value="<?php echo jtext::_('COM_SIMPLECHATSUPPORT_SEND');?>" onclick="javascript:sendChatText();" />
						<br />
						<img onClick='smile(":)")' src='../administrator/components/com_simplechatsupport/images/smile/smile.png' border="0" alt=":)">
						<img onClick='smile(":@")' src='../administrator/components/com_simplechatsupport/images/smile/angry.png' border="0">
						<img onClick='smile(":d")' src='../administrator/components/com_simplechatsupport/images/smile/lol.png' border="0">
						<img onClick='smile(":(")' src='../administrator/components/com_simplechatsupport/images/smile/sad.png' border="0">
						<img onClick='smile(":o")' src='../administrator/components/com_simplechatsupport/images/smile/surprised.png' border="0">										
						<img onClick='smile(";)")' src='../administrator/components/com_simplechatsupport/images/smile/wink.png' border="0">
					</p>
				</form>
			</td>
			<td style="width: 15px;">&nbsp;</td>
			<td style="width: 250px;" valign="top">
				<fieldset>
					<legend>
						<?php echo jtext::_('COM_SIMPLECHATSUPPORT_TEMPLATE');?>
						<span class="pull-right">
							<a class="small" href="index.php?option=com_simplechatsupport&controller=messages&task=add"><span class="icon-new"></span></a>
							<a class="small" href="index.php?option=com_simplechatsupport&controller=messages"><span class="icon-options"></span></a>
						</span>
					</legend>
					<div id="div_template_messages" class="chat-list">
						<?php
						for($i=0; $i < count( $this->template_messages ); $i++) {
							$row = $this->template_messages[$i];
							echo '<a href="javascript:setTemplateMessage('.$i.');">'.$row->title.'</a>';
						}
						?>
					</div>
				</fieldset>				
			</td>
		</tr>
	</table>
	<audio id="soundChat" preload="none">
	  <source src="components/com_simplechatsupport/sounds/complete.wav" type="audio/wav">
	  <source src="components/com_simplechatsupport/sounds/complete.mp3" type="audio/mpeg">
	</audio>
	<audio id="soundNotification" preload="none">
	  <source src="components/com_simplechatsupport/sounds/notify.wav" type="audio/wav">
	  <source src="components/com_simplechatsupport/sounds/notify.mp3" type="audio/mpeg">
	</audio>
	<script>
		setStatus(2);
		startChat();
		getChatRoom();
		window.onbeforeunload = function() {
			if(online == 1 && chat_reset == 0) {
				return '<?php echo jtext::_('COM_SIMPLECHATSUPPORT_CLOSE_WINDOW'); ?>';
			}
		}
	</script>