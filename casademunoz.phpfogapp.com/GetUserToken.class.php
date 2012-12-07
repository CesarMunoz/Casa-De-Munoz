<?php
	/**
	* This will retrieve an end users auth key
	* @author Cesar Munoz
	* @license GNU Public License
	*/

	class GetUserTokenException extends Exception {}

	class GetUserToken {
		private $u_id = '1';
		private $db_host;
		private $db_username;
		private $db_password;
		private $db_name;

		public function  __construct(/*$id = false*/){
			//$this->$UserID = $id;
		}

		/**
		 * SetAccessToken
		 * Basic setter function,
		 * @param String $id A Foursquare user id
		 */
		public function SetUserVars($id, $h, $u, $p, $n){
			echo '<br>id: '.$id;
			echo '<br>h: '.$h;
			echo '<br>u: '.$u;
			echo '<br>p: '.$p;
			echo '<br>n: '.$n;
			$this->u_id = $id;
			$this->db_host = $h;
			$this->db_username = $u;
			$this->db_password = $p;
			$this->db_name = $n;
			echo '<br>user_id: '.$this->u_id;
			echo '<br>db_host: '.$this->db_host;
			echo '<br>db_username: '.$this->db_username;
			echo '<br>db_password: '.$this->db_password;
			echo '<br>db_name: '.$this->db_name;
		}
	
		public function GetToken(){
			$connect = mysql_connect($this->db_host, $this->db_username, $this->db_password) or die (mysql_error());
			echo "Connected to MySQL<br/>";
			mysql_select_db($this->db_name) or die (mysql_error());
			echo "Connected to Database<br/>";

			$SQL = "SELECT * FROM 4SQ_USER_TOKENS ";
			$result = mysql_query($SQL);
			$num = mysql_numrows($result);
			echo "Number of rows : " . $num . "<br/>";
			
			for($i=0; $i<$num; $i++){
				$id_in_table = mysql_result( $result, $i, "user_id");
				if($this->u_id == $id_in_table){
					$end_user_token = mysql_result( $result, $i, "user_token" );
					break;
				}
			}

			mysql_close($connect);

			return $end_user_token;
			//return $var = $this->UserID;
		}
	}
?>