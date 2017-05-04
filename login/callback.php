<?php
	session_start();

	// We need the callback to get the redirect_uri from the user
	$API_CALLBACK = isset($_SESSION["callback"]) ? $_SESSION["callback"] : '';

	// Unset the api_key so that nobody can reference it again through the session
	// unset($_SESSION["api_key"]); FOR DEBUG;
	// Unset the api_secret so that nobody can reference it again through the session
	// unset($_SESSION["api_secret"]); FOR DEBUG
?>
<script>
	// Get the value after the hashtag
	var type = window.location.hash.substr(1);
	// callback uri from the user
	var callback_uri = "<?php echo $API_CALLBACK; ?>";
	if(type !== '' && callback_uri !== '') {
		token = type.split("=");
		// redirect the user to the right page
		window.location.href = callback_uri + '?access_token=' + token[1];
	}
</script>