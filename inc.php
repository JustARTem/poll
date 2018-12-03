<?php
	define('DB_FILENAME','poll.db');

	/** Creates new tables if not exists
	 *
	 */
	function InitDB(){
		$db = new SQLite3(DB_FILENAME);
		if ($db){
			$db->exec('CREATE TABLE IF NOT EXISTS `answers` ( `id` INTEGER, `answer` TEXT, `question_id` INTEGER, PRIMARY KEY(`id`) )');
			$db->exec('CREATE TABLE IF NOT EXISTS `questions` ( `id` INTEGER, `question` TEXT, PRIMARY KEY(`id`) )');
			$db->exec('CREATE TABLE IF NOT EXISTS `voters` ( `id` INTEGER, `fullname` TEXT NOT NULL, `question_id` INTEGER, `answer_id` INTEGER, PRIMARY KEY(`id`) )');
			$db->close();
		}
	}

	/** Open DB
	 * @param int $flag
	 *
	 * @return SQLite3
	 */
	function OpenDB($flag = SQLITE3_OPEN_READWRITE){
			$db = new SQLite3(DB_FILENAME,$flag);
			return $db;
	}

	/** Adds new Poll
	 * @param $question
	 * @param $arAnswers
	 *
	 * @return int - ID of added Question or 0 if error
	 */
	function AddPoll($question,$arAnswers)
	{
		$res = 0;
		if ($db = OpenDB()){
			if ($question && count($arAnswers)>1) {
				$question = filter_var($question,FILTER_SANITIZE_STRING);
				$arAnswers = filter_var_array($arAnswers,FILTER_SANITIZE_STRING);
				$db->exec( "INSERT INTO questions (question) VALUES ('$question')" );
				$questionId = $db->lastInsertRowid();
				foreach ($arAnswers as $answer){
					$db->exec( "INSERT INTO answers (answer,question_id) VALUES ('$answer',$questionId)" );
				}
				$res = $questionId;
			}
			$db->close();
		}
		return $res;
	}

	/** Gets Poll by ID
	 * @param $questionId
	 *
	 * @return array - Question and Answers
	 */
	function GetPoll($questionId){
		$arPoll = [];
		if ($db = OpenDB(SQLITE3_OPEN_READONLY)) {
			if ( $questionId > 0 ) {
				$arResult             = $db->querySingle( "SELECT question FROM questions WHERE id=$questionId" );
				if ($arResult) $arPoll[ 'question' ] = $arResult;
				$result               = $db->query( "SELECT answer,id FROM answers WHERE question_id=$questionId" );
				while( $arResult = $result->fetchArray() ){
					$arPoll[ 'answers' ][ $arResult[ 'id' ] ] = $arResult[ 'answer' ];
				}
			}
			$db->close();
		}
		return $arPoll;
	}

	/** Adds new vote
	 * @param $questionId
	 * @param $fullname
	 * @param $answerId
	 *
	 * @return int - 1 if success else 0. -1 = db error.
	 */
	function AddVote($questionId,$fullname,$answerId)
	{
		$res = 0;
		if ($db = OpenDB()) {
			if ( $questionId > 0 && $answerId > 0 && $fullname ) {
				$fullname = filter_var( $fullname, FILTER_SANITIZE_STRING );
				$db->enableExceptions(true);
				try{
					$db->exec( "INSERT INTO voters (fullname,question_id,answer_id) VALUES ('$fullname',$questionId,$answerId)" );
					$res = 1;
				} catch(Exception $e){
					$res =-1;
				}
			}
			$db->close();
		}
		return $res;
	}

	/** Gets results for Poll by QuestionID
	 * @param $questionId
	 *
	 * @return array - Names of Voters and their answers
	 */
	function GetPollResult($questionId)
	{
		$res = [];
		if ($db = OpenDB(SQLITE3_OPEN_READONLY)) {
			if ( $questionId > 0 ) {
				$db->enableExceptions(true);
				try{
					$result = $db->query( "SELECT id,fullname,answer_id FROM voters WHERE question_id=$questionId ORDER BY id DESC" );
					while( $arResult = $result->fetchArray() ){
						$res[ $arResult[ 'fullname' ] ] = $arResult[ 'answer_id' ];
					}
				} catch(Exception $e){
					$res = -1;
				}
			}
			$db->close();
		}
		return $res;
	}
