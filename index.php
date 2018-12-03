<?php 	include_once($_SERVER['DOCUMENT_ROOT'].'/poll/inc.php');
	InitDB(); //Creates new tables if not exists
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
			Create your Poll
		</div>
	</div>
	<div class="page__image">
		<div class="page__task-title">
			Create your Poll
		</div>
	</div>
	<div class="page__content page__content--padding">
		<div class="poll">
			<form name="add-poll" method="post" action="">
				<table class="poll-table">
					<thead>
					<tr>
						<th>Question:</th>
						<th>
							<input type="text" name="question" value="" class="input-text">
						</th>
					</tr>
					</thead>
					<tbody id="answers">
					<tr id="btn_add_row">
						<td class="poll-table__plus">
							<button class="btn btn--plus" id="btn_add">
								+
							</button>
						</td>

						<td> </td>
					</tr>
					</tbody>
				</table>

				<button class="btn btn--start" id="btn_start">
					Start
				</button>
			</form>
		</div>
	</div>
</div>
<script>
	const min_answers_count = 2; //initial number of input controls for answer
	let n = 1;

	/** Adds another input control for answer*/
	btn_add.onclick = function (){
		//el.preventDefault();
		//console.log(document.getElementsByClassName('poll-table__plus'));
		let a_tr = document.createElement('TR');
		answers.insertBefore(a_tr,btn_add_row);
		let a_th = document.createElement('TH');
		let a_td = document.createElement('TD');
		let a_in = document.createElement('INPUT');
		a_in.setAttribute('type', 'text');
		a_in.className = 'input-text';
		a_in.setAttribute('name', 'answers['+n.toString()+']');
		a_tr.appendChild(a_th);
		a_tr.appendChild(a_td);
		a_td.appendChild(a_in);
		a_th.innerHTML = 'Answer '+n+':';
		n +=1;
		return false;
	};

	/** Shows initial input controls for answer*/
	for (let i= 0; i < min_answers_count; i++) {
		btn_add.onclick();
	}


	/** Saves the Poll and moves to a view-page */
	btn_start.onclick = function (){
		const formData = new FormData(document.forms[0]);
		if (!!formData.get('question') && !!formData.get('answers[1]') && !!formData.get('answers[2]')){
			let req = new XMLHttpRequest();
			req.open('POST', 'ajax.php', true);
			req.setRequestHeader('accept', 'application/json');
			formData.append('action','addpoll');
			req.send(formData);
			req.onreadystatechange = function () {
				if (req.readyState < 4) return;
				if (req.status == 200){
					if (!!req.responseText){
						if (parseInt(req.responseText)>0){
							window.location.replace('poll.php?id='+req.responseText);
						} else alert('Wrong Poll data');
					} else alert('Empty Poll data');
				}
			}
		} else {
			alert('Please enter your question and  possible answers (at least two)');
		}
		return false;
	}
</script>
</body></html>
