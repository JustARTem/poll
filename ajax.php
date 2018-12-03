<?
	/**
	 *  Process ajax-requests
	 */

	include_once('inc.php');

	switch ($_REQUEST['action']){
		case 'addpoll': //Adds new Poll
			if (isset($_REQUEST['question']) && isset($_REQUEST['answers'])) {
				echo  AddPoll( trim( $_REQUEST[ 'question' ] ), $_REQUEST[ 'answers' ] );
			}
			break;
		case 'addvote': //Adds new vote
			if (isset($_REQUEST['question_id']) && isset($_REQUEST['fullname']) && isset($_REQUEST['answer_id'])) {
				echo  AddVote( intval( $_REQUEST[ 'question_id' ] ),  trim($_REQUEST[ 'fullname' ]),intval($_REQUEST[ 'answer_id' ] ));
			}
			break;
		case 'getpollresult': //Gets results for Poll by QuestionID
			if (isset($_REQUEST['id'])) {
				$questionId = intval( $_REQUEST[ 'id' ] );
				$arResult = GetPollResult( $questionId);
				if (is_array($arResult)) {
					echo json_encode($arResult);
				} else header("HTTP/1.0 400");

			}
			break;
	}
