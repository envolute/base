<?php
/**
 * @copyright	Copyright ? 2014 - All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @author		Joompolitan -> Envolute
 * @author mail	dev@envolute.com
 * @website		http://www.envolute.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// current user information
$user = JFactory::getUser();			

//../components/com_simplechatsupport/images/smile/smile.png
$smile1 = JURI::base()."components/com_simplechatsupport/images/smile/smile.png";
$smile2 = JURI::base()."components/com_simplechatsupport/images/smile/angry.png";
$smile3 = JURI::base()."components/com_simplechatsupport/images/smile/lol.png";
$smile4 = JURI::base()."components/com_simplechatsupport/images/smile/sad.png";
$smile5 = JURI::base()."components/com_simplechatsupport/images/smile/surprised.png";
$smile6 = JURI::base()."components/com_simplechatsupport/images/smile/wink.png";

$chatURL = JURI::base()."components/com_simplechatsupport/getChat.php";
include_once JPATH_BASE.'/components/com_simplechatsupport/getStatus.php';
$status = statusChat::getStatus();

$tpl_sm = (isset($_REQUEST['sm']) && $_REQUEST['sm'] == 1) ? 1 : 0;
$widget = (isset($_REQUEST['widget']) && $_REQUEST['widget'] == 1) ? 1 : 0;
$col = ($tpl_sm) ? '6' : '4';
$sbj = ($tpl_sm) ? '12' : '8';
?>

<div id="jsl-app">
<?php
if($status == 0) :
?>
	<!-- show email form -->
	<script language="JavaScript" type="text/javascript">
	<!--
	function validate(){
		if ( 
		( document.emailForm.email.value == "" ) || 
		( document.emailForm.email.value.search("@") == -1 ) || 
		( document.emailForm.email.value.search("[.*]" ) == -1 )
		) {
			alert( "<?php echo addslashes( JText::_('COM_SIMPLECHATSUPPORT_FORM_NC') ); ?>" );
		} else if ( ( document.emailForm.email.value.search(";") != -1 ) || ( document.emailForm.email.value.search(",") != -1 ) || ( document.emailForm.email.value.search(" ") != -1 ) ) {
			alert( "<?php echo addslashes( JText::_('COM_SIMPLECHATSUPPORT_ONE_EMAIL') ); ?>" );
		} else {
			document.emailForm.submit();
		}
	}
	//-->
	</script>
				
	<div id="jsl-mail-action">
		<form action="<?php echo JURI::root().'index.php?option=com_simplechatsupport&task=sendMail'; ?>" method="post" name="emailForm" id="emailForm" class="form-validate">
			<div class="alert alert-warning small strong">
				<?php echo ($this->message_off == '') ? JText::_('COM_SIMPLECHATSUPPORT_CHAT_OFFLINE_MESSAGE') : $this->message_off; ?>
			</div>
			
			<?php if($user->guest) : ?>
			<div class="row">
				<div class="col-sm-<?php echo $col?>">
					<div class="form-group">
						<?php if(!$widget) : ?>
							<label for="name"><?php echo JText::_('COM_SIMPLECHATSUPPORT_CONTACT_NAME'); ?>:</label>
						<?php
						else :
							$placeholder = JText::_('COM_SIMPLECHATSUPPORT_CONTACT_NAME');
						endif;
						?>
						<input type="text" name="name" id="name" class="form-control" placeholder="<?php echo $placeholder?>" />
					</div>
				</div>
				<div class="col-sm-<?php echo $col?>">
					<div class="form-group">
						<?php if(!$widget) : ?>
							<label for="email"><?php echo JText::_('COM_SIMPLECHATSUPPORT_CONTACT_EMAIL'); ?>:</label>
						<?php
						else :
							$placeholder = JText::_('COM_SIMPLECHATSUPPORT_CONTACT_EMAIL');
						endif;
						?>
						<input type="email" name="email" id="email" class="form-control" placeholder="<?php echo $placeholder?>" />
					</div>
				</div>
			
			</div>
			<?php
			else :
				echo '
				<input type="hidden" name="name" id="name" value="'.$user->name.'" />
				<input type="hidden" name="email" id="email" value="'.$user->email.'" />
				';
			endif;
			?>
			
			<div class="row">
				<div class="col-sm-<?php echo $sbj?>">
					<div class="form-group">
						<?php if(!$widget) : ?>
							<label for="subject"><?php echo JText::_('COM_SIMPLECHATSUPPORT_CONTACT_SUBJECT'); ?>:</label>
						<?php
						else :
							$placeholder = JText::_('COM_SIMPLECHATSUPPORT_CONTACT_SUBJECT');
						endif;
						?>
						<input type="text" name="subject" id="subject" class="form-control" placeholder="<?php echo $placeholder?>" />
					</div>
				</div>
			</div>
			
			<div class="form-group">
				<label for="text"><?php echo JText::_('COM_SIMPLECHATSUPPORT_CONTACT_MESSAGE'); ?>:</label>
				<textarea rows="4" name="text" id="text" class="form-control"></textarea>
			</div>
			<div class="form-actions">
				<input type="button" name="send" value="<?php echo JText::_('COM_SIMPLECHATSUPPORT_BUTTON'); ?>" class="btn btn-primary validate" onclick="validate()" />
				<input type="hidden" name="option" value="com_simplechatsupport" />
				<input type="hidden" name="op" value="sendMail" />
				<input type="hidden" name="sm" value="<?php echo $tpl_sm?>" />
				<?php if($widget) echo '<input type="hidden" name="widget" id="widget" value="1" />'; ?>
				<?php echo JHTML::_( 'form.token' ); ?>
			</div>
		</form>
	</div>

<?php else : ?>
	
	<!-- show live chat -->
	<style type="text/css" media="screen">
		#div_chat {
			height: 300px;
			font-size: 12px;
			background: #ffe!important;
			overflow: auto;
		}
		
		#div_chat em.hello {
			display: block;
		    	padding-bottom: 5px;
		    	margin-bottom: 8px;
		    	font-size: <?php echo (widget) ? '14px' : '15px'; ?>;
			border-bottom: 1px dashed #ccc;
		}
		#div_chat .chat-msg {
			padding: 5px 0;
		}
		#div_chat .chat-msg em {
			color: #f80;
			font-size: 10px;
		}
		#div_chat .chat-msg.chat-operator strong {
			color: #f80;
		}
		#txt_name.form-control {
			width: auto;		
		}
		#txt_message.form-control,
		#btn_send_chat.btn {
			<?php echo (widget) ? 'height: 60px; font-size: 12px; padding: 4px;' : 'height: 50px;'; ?>
		}
		#btn_send_chat.btn {
			font-weight: bold;		
		}
		#chat_tools #btn_reset_chat {
			margin-left: 20px;
		}
	</style>
	<script language="JavaScript" type="text/javascript">
		//var receiveReq_ = getXmlHttpRequestObject();
		var receiveReq = getXmlHttpRequestObject();
		var lastMessage = 0;
		var id = 0;
		var mTimer;
		//Function for initializating the page.
		function startChat() {
			//Set the focus to the Message Box.
			if(document.getElementById('txt_name').value == '') document.getElementById('txt_name').focus();
			else document.getElementById('txt_message').focus();
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
		
		//Gets the current messages from the server
		function getChatText() {
			if(id == 0)
			{
				return;
			}
		
			if (receiveReq.readyState == 4 || receiveReq.readyState == 0) {
				receiveReq.open("GET", '<?php echo $chatURL;?>?chat=' + id + '&last=' + lastMessage, true);
				receiveReq.onreadystatechange = handleReceiveChat; 
				receiveReq.send(null);
			}			
		}
		//Add a message to the chat server.
		function sendChatText() {
			if(document.getElementById('txt_name').value == '') {
				alert("Por favor, informe seu nome!");
				document.getElementById('txt_name').focus();	
				return;
			}
			if(document.getElementById('txt_message').value == '') {
				alert("Você não digitou a mensagem");
				document.getElementById('txt_message').focus();	
				return;
			}
			if (receiveReq.readyState == 4 || receiveReq.readyState == 0) {
				receiveReq.open("POST", '<?php echo $chatURL;?>?chat=' + id + '&last=' + lastMessage, true);
				receiveReq.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
				receiveReq.onreadystatechange = handleSendChat; 
				var param = 'message=' + document.getElementById('txt_message').value;
				param += '&name=' + document.getElementById('txt_name').value;
				param += '&chat=' + id;
				receiveReq.send(param);
				document.getElementById('txt_message').value = '';
			}
			document.getElementById('txt_message').focus();
			chatFocus();						
		}
		//When our message has been sent, update our page.
		function handleSendChat() {
			//Clear out the existing timer so we don't have 
			//multiple timer instances running.
			if (receiveReq.readyState == 4) {
				var chat_div = document.getElementById('div_chat');
				var xmldoc = receiveReq.responseXML;
				var message_nodes = xmldoc.getElementsByTagName("message");
				var n_messages = message_nodes.length
				for (i = 0; i < n_messages; i++) {
					var user_node = message_nodes[i].getElementsByTagName("user");
					var text_node = message_nodes[i].getElementsByTagName("text");
					var time_node = message_nodes[i].getElementsByTagName("time");
					
					chat_div.innerHTML += '<div class="chat-msg chat-user"><strong>' + user_node[0].firstChild.nodeValue + '</strong> - <em>' + time_node[0].firstChild.nodeValue + '</em><br /><span>' + text_node[0].firstChild.nodeValue + '</span></div>';					
					
					chat_div.innerHTML = chat_div.innerHTML.replace(':)', '<img src=\'<?php echo $smile1;?>\'>');
					chat_div.innerHTML = chat_div.innerHTML.replace(':@', '<img src=\'<?php echo $smile2;?>\'>');
					chat_div.innerHTML = chat_div.innerHTML.replace(':d', '<img src=\'<?php echo $smile3;?>\'>');
					chat_div.innerHTML = chat_div.innerHTML.replace(':(', '<img src=\'<?php echo $smile4;?>\'>');
					chat_div.innerHTML = chat_div.innerHTML.replace(':o', '<img src=\'<?php echo $smile5;?>\'>');	
					chat_div.innerHTML = chat_div.innerHTML.replace(';)', '<img src=\'<?php echo $smile6;?>\'>');
											
					chat_div.scrollTop = chat_div.scrollHeight;
					lastMessage = (message_nodes[i].getAttribute('id'));
				}
				
				var room = xmldoc.getElementsByTagName("room");
				id = room[0].getAttribute('id');
			}
			
			if(!mTimer) mTimer = setTimeout('getChatText();',1500); //Refresh our chat in 1,5 seconds
		}
		
		//Function for handling the return of chat text
		var count = 0;
		function handleReceiveChat() {
			if (receiveReq.readyState == 4) {
				var chat_div = document.getElementById('div_chat');
				var xmldoc = receiveReq.responseXML;
				var message_nodes = xmldoc.getElementsByTagName("message");
				var n_messages = message_nodes.length;
				if(count != 0 && count != n_messages) document.getElementById('soundChat').play();
				count = n_messages;
				for (i = 0; i < n_messages; i++) {
					var user_node = message_nodes[i].getElementsByTagName("user");
					var text_node = message_nodes[i].getElementsByTagName("text");
					var time_node = message_nodes[i].getElementsByTagName("time");
					
					chat_div.innerHTML += '<div class="chat-msg chat-operator"><strong>' + user_node[0].firstChild.nodeValue + '</strong> - <em>' + time_node[0].firstChild.nodeValue + '</em><br /><span>' + text_node[0].firstChild.nodeValue + '</span></div>';
					chat_div.innerHTML = chat_div.innerHTML.replace(':)', '<img src=\'<?php echo $smile1;?>\'>');
					chat_div.innerHTML = chat_div.innerHTML.replace(':@', '<img src=\'<?php echo $smile2;?>\'>');
					chat_div.innerHTML = chat_div.innerHTML.replace(':d', '<img src=\'<?php echo $smile3;?>\'>');
					chat_div.innerHTML = chat_div.innerHTML.replace(':(', '<img src=\'<?php echo $smile4;?>\'>');
					chat_div.innerHTML = chat_div.innerHTML.replace(':o', '<img src=\'<?php echo $smile5;?>\'>');	
					chat_div.innerHTML = chat_div.innerHTML.replace(';)', '<img src=\'<?php echo $smile6;?>\'>');
											
					chat_div.scrollTop = chat_div.scrollHeight;
					lastMessage = (message_nodes[i].getAttribute('id'));
				
					<?php
					// set call to module
					if($widget) echo 'jQuery("#simplechatsupport-widget", window.parent.document).addClass("call");';
					?>
				}
				var room = xmldoc.getElementsByTagName("room"); 
				id = room[0].getAttribute('id');
			}
			
			if(mTimer) clearTimeout(mTimer);
			mTimer = setTimeout('getChatText();', 1500); //Refresh our chat in 1,5 seconds
		}
		//This functions handles when the user presses enter.  Instead of submitting the form, we
		//send a new message to the server and return false.
		function blockSubmit() {
			sendChatText();
			return false;
		}
		//This cleans out the database so we can start a new chat session.
		function resetChat() {
			if (receiveReq.readyState == 4 || receiveReq.readyState == 0) {
				receiveReq.open("POST", '<?php echo $chatURL;?>?chat=' + id + '&last=' + lastMessage, true);
				receiveReq.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
				receiveReq.onreadystatechange = handleResetChat; 
				var param = 'action=reset';
				receiveReq.send(param);
				document.getElementById('txt_message').value = '';
			}							
		}
		//This function handles the response after the page has been refreshed.
		function handleResetChat() {
			document.getElementById('div_chat').innerHTML = '';
			getChatText();
		}	
		
		function smile(code) {				
			this.code = code;
			document.getElementById('txt_message').value += code
		}
		
		function chatFocus() {
			jQuery("#simplechatsupport-widget", window.parent.document).removeClass('call');
		}
		
	</script>

	<div id="jsl-support-action">
		<form id="frmmain" name="frmmain" onsubmit="return blockSubmit();">
			<?php if(!$widget) : ?>
				<h4 id="p_status">
					<img src="<?php echo JUri::base(true) . '/components/com_simplechatsupport/images/user.png' ?>">
					<?php echo ($this->message_on == '') ? JText::_('COM_SIMPLECHATSUPPORT_CHAT_ONLINE_MESSAGE') : $this->message_on; ?>
				</h4>
			<?php endif; ?>
			<div id="div_chat" class="well well-sm bottom-space">
				<em class="hello">
					<?php echo ($this->message == '') ? JText::_('COM_SIMPLECHATSUPPORT_CHAT_GREETING_MESSAGE') : $this->message; ?>
				</em>
			</div>
			<?php
			if($user->guest) :
				echo '<p><input type="text" id="txt_name" name="txt_name" class="form-control" placeholder="'.JText::_('COM_SIMPLECHATSUPPORT_NAME').'"/></p>';
			else :
				echo '<input type="hidden" id="txt_name" name="txt_name" value="'.$user->name.'" />';
			endif;
			?>
			<div class="row">
				<div class="col-sm-9">
					<div class="form-group">
						<textarea id="txt_message" name="txt_message" class="form-control" oninput="chatFocus()"></textarea>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<input type="button" class="btn btn-primary btn-block hidden-xs" name="btn_send_chat" id="btn_send_chat" value="<?php echo JText::_('COM_SIMPLECHATSUPPORT_BUTTON_SEND'); ?>" onclick="javascript:sendChatText();" />
					</div>
				</div>
				
			</div>
			<div id="chat_tools">
				<img onClick='smile(":)")' src='<?php echo $smile1;?>' border="0" alt=":)">
				<img onClick='smile(":@")' src='<?php echo $smile2;?>' border="0">
				<img onClick='smile(":d")' src='<?php echo $smile3;?>' border="0">
				<img onClick='smile(":(")' src='<?php echo $smile4;?>' border="0">
				<img onClick='smile(":o")' src='<?php echo $smile5;?>' border="0">
				<img onClick='smile(";)")' src='<?php echo $smile6;?>' border="0">
				<span class="hidden-xs">
					<input type="button" class="btn btn-xs btn-default" name="btn_reset_chat" id="btn_reset_chat" value="<?php echo JText::_('COM_SIMPLECHATSUPPORT_BUTTON_RESET'); ?>" onclick="javascript:resetChat();" />
				</span>
				<input type="button" class="btn btn-sm btn-primary pull-right visible-xs" name="btn_send_chat" id="btn_send_chat_xs" value="<?php echo JText::_('COM_SIMPLECHATSUPPORT_BUTTON_SEND'); ?>" onclick="javascript:sendChatText();" />
			</div>
		</form>
		
		<audio id="soundChat" preload="none">
			<source src="components/com_simplechatsupport/sounds/complete.wav" type="audio/wav">
			<source src="components/com_simplechatsupport/sounds/complete.mp3" type="audio/mpeg">
		</audio>
		<audio id="soundNotification" preload="none">
			<source src="components/com_simplechatsupport/sounds/notify.wav" type="audio/wav">
			<source src="components/com_simplechatsupport/sounds/notify.mp3" type="audio/mpeg">
		</audio>
		<script>
			startChat();
		</script>
	</div>

<?php endif; ?>

</div>