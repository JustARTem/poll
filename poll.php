<? include_once('inc.php');
	if (isset($_GET['id'])){
		$questionId = intval(trim($_GET['id']));
		$arPoll = GetPoll($questionId);
	}
?>
<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link href="assets/style.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Roboto+Condensed:300,400,400i,700" media="all">
	<title>XIAG test task</title>
	<meta name="robots" content="noindex,nofollow">
	<meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0">
	<link rel="shortcut icon" href="https://test-task.xiag.ch/images/favicon.ico" type="image/x-icon">
</head>
<body>
<div class="page">
	<div class="page__header">
		<div class="page__logo">
			<a href="https://www.xiag.ch/" target="_blank">
				<img src="assets/page-logo.png" alt="XIAG AG">
			</a>
		</div>
		<div class="page__task-name">
			Make your choice
		</div>
	</div>
	<div class="page__image">
		<div class="page__task-title">
			Make your choice
		</div>
	</div>
	<div class="page__content page__content--padding">
		<div class="poll">
			<? if ($arPoll['answers']){?>
			<h1 id="question">
				<?=$arPoll['question']?>
			</h1>

			<div class="ex2-question">
				<form name="add-vote" method="post" action="">
					<div class="ex2-question__label">
						Your name:
					</div>
					<div class="ex2-question__input">
						<input type="text" name="fullname" class="input-text" >
					</div>
					<div class="ex2-question__answer">
						<?
						foreach ($arPoll['answers'] as $key => $value){
							?>
							<label>
								<input type="radio" name="answer_id" value="<?=$key?>">
								<?=$value?>
							</label>
						<?
						}
						?>
					</div>
					<div class="ex2-question__submit">
						<input type="submit" class="btn" value="Submit" id="btn">
					</div>
				</form>
			</div>
			<h1>
				Results
			</h1>
			<br>
			<table class="ex2-table">
			<thead>
			</thead>
				<tbody>
				</tbody>
			</table>

				<script>
					const refresh_interval = 1000; //Refresh interval in ms for Results table.
					const cookie_string = 'poll_voted=1';
					//const voted = document.cookie.includes(cookie_string);
					const voted = false;
					const answers = {
						<?foreach ($arPoll['answers'] as $key => $value) echo "'$key':'$value',";?>
					};
					let tempresult = {},
						oldresult = {};

					let result_head_html = '<tr><th>Name</th>';
					for (let answer_id in answers){
						result_head_html +='<th>'+answers[answer_id]+'</th>'
					}
					result_head_html +='</tr>';

					getResult();
					window.setInterval('getResult()',refresh_interval);


					/** Adds vote for current Poll */
					if (!voted){ //Checks if user is voting first time
						btn.onclick = function (){
							const formData = new FormData( document.forms[0] );
							if (!voted){
								if ( !!formData.get('fullname') && parseInt(formData.get('answer_id'))>0){
									let req = new XMLHttpRequest();
									req.open( 'POST', 'ajax.php', true );
									req.setRequestHeader( 'accept', 'application/json' );
									formData.append('action','addvote');
									formData.append('question_id','<?=$questionId?>');
									req.send( formData );
									req.onreadystatechange = function(){
										if( req.readyState < 4 ) return;
										if( req.status == 200 ){
											if( parseInt( req.responseText ) == 1 ){
												expiry = new Date();
												expiry.setTime( expiry.getTime() + 100 * 365 * 24 * 3600 * 1000 );
												document.cookie = cookie_string + "; expires=" + expiry.toGMTString();
												console.log( 'Added vote for ', formData.get( 'fullname' ) );
												getResult();
											} else {
												alert('Error during adding vote. Please, try again.' );
											}
										}
									}
								} else {
									alert('Please enter your name and answer');
								}
							} else {
								alert('It is possible to vote only once for a person.');
							}
							return false;
						}
					} else {
						hideForm();
					}


					/** Gets all results of Poll */
					function getResult(){
						let req = new XMLHttpRequest();
						req.open('GET', 'ajax.php?action=getpollresult&id=<?=$questionId?>', true);
						req.responseType = 'json';
						req.send();
						req.onreadystatechange = function () {
							if (req.readyState < 4) return;
							if (req.status == 200){
								tempresult = req.response;
								result_head = document.getElementsByTagName('thead')[0];
								if (JSON.stringify(tempresult) !== '[]') { //shows head of results table, if results are exists
									result_head.innerHTML = result_head_html;
								} else {
									result_head.innerHTML = 'No results yet. Be the first';
									return;
								}
								let temphtml = '';
								if (JSON.stringify(oldresult) !== JSON.stringify(tempresult)){ //checks for changes of old results
									result_body = document.getElementsByTagName('tbody')[0];
									for (let fullname in tempresult){
										temphtml +='<tr><td>'+fullname+'</td>';
										for (let answer_id in answers){
											temphtml +='<td>';
											if (tempresult[fullname]==answer_id) {
												temphtml +='x';
											}
											temphtml +='</td>';
										}
									}
									oldresult = tempresult;
									result_body.innerHTML = temphtml;
								}
							}
						};
						return false;
					}

					/** Hides input form for voters */
					function hideForm(){
						document.getElementById('question').style = 'padding-bottom: 20px; margin-bottom:20px; border-bottom: solid 1px #eee;';
						document.getElementsByClassName('ex2-question')[0].remove();
					}
				</script>
			<?} else {?>
				<h3>Poll is not found. Would you like to <a href="/poll/">create your own Poll?</a></h3>
			<?}?>
		</div>
	</div>
</div>


</body></html>

