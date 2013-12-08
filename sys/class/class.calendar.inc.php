<?php
/**
 * Builds and manipulates an events calendar
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to the MIT License, available
 * at http://www.opensource.org/licenses/mit-license.html
 *
 * @author Jason Lengstorf <jason.lengstorf@ennuidesign.com>
 * @copyright 2009 Ennui Design
 * @license http://www.opensource.org/licenses/mit-license.html
 */
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	@set_time_limit(1000);
	@set_magic_quotes_runtime(0);
class Calendar extends DB_Connect{
  /**
   * The date from which the calendar should be built
   *
   * Stored in YYYY-MM-DD HH:MM:SS format
   *
   * @var string the date to use for the calendar
   */
  private $_useDate;
  
  /**
   * The month for which the calendar is being built
   *
   * @var int the month being used
   */
  private $_m;
  
  /**
   * The year from which the month's start day is selected
   *
   * @var int the year being used
   */
  private $_y;
  
  /**
   * The number of days in the month being used
   *
   * @var int the number of days in the month
   */
  private $_daysInMonth;
  
  /**
   * The index of the day of the week the month starts on (0-6)
   *
   * @var int the day of the week the month starts on
   */
  private $_startDay;
  
  /**
   * Creates a database object and stores relevant data
   *
   * Upon instantiation, this class accepts a database object
   * that, if not null, is stored in the object's private $_db
   * property. If null, a new PDO object is created and stored
   * instead.
   *
   * Additional info is gathered and stored in this method,
   * including the month from which the calendar is to be built,
   * how many days are in said month, what day the month starts
   * on, and what day it is currently.
   *
   * @param object $dbo a database object
   * @param string $useDate the date to use to build the calendar
   * @return void
   */
  public function __construct($dbo=NULL, $useDate=NULL){
	/*
     * Call the parent constructor to check for
     * a database object
     */
    parent::__construct($dbo);
	
    /*
     * Gather and store data relevant to the month
     */
    if ( isset($useDate) ){
      $this->_useDate = $useDate;
    }else{
      $this->_useDate = date('Y-m-d H:i:s');
    }
	
	/*
     * Convert to a timestamp, then determine the month
     * and year to use when building the calendar
     */
    $ts = strtotime($this->_useDate);
    $this->_m = date('m', $ts);
    $this->_y = date('Y', $ts);
	
	/*
     * Determine how many days are in the month
     */
    $this->_daysInMonth = cal_days_in_month(CAL_GREGORIAN,$this->_m,$this->_y);
	
	/*
     * Determine what weekday the month starts on
     */
    $ts = mktime(0, 0, 0, $this->_m, 1, $this->_y);
    $this->_startDay = date('w', $ts);
  }

  public function buildCalendar(){
    /*
     * Determine the calendar month and create an array of
     * weekday abbreviations to label the calendar columns
     */
    $cal_month = date('F Y', strtotime($this->_useDate));
    $cal_id = date('Y-m', strtotime($this->_useDate));
    $weekdays = array('星期日', '星期一', '星期二','星期三', '星期四', '星期五', '星期六');
	
	/*
     * Add a header to the calendar markup
     */
    $html = "\n\t<h2 id=\"month-$cal_id\">$cal_month</h2>";
    for ( $d=0, $labels=NULL; $d<7; ++$d ){
      $labels .= "\n\t\t<li>" . $weekdays[$d] . "</li>";
    }
    $html .= "\n\t<ul class=\"weekdays\">". $labels . "\n\t</ul>";
	
    /*
     * Load events data
     */
    $events = $this->_createEventObj();
    
	/*
     * Create the calendar markup
     */
    $html .= "\n\t<ul>"; // Start a new unordered list
    for ( $i=1, $c=1, $t=date('j'), $m=date('m'), $y=date('Y');$c<=$this->_daysInMonth; ++$i ){      
	  /*
       * Apply a "fill" class to the boxes occurring before
       * the first of the month
       */
      $class = $i<=$this->_startDay ? "fill" : NULL;
	  
      /*
       * Add a "today" class if the current date matches
       * the current date
       */
      if ( $c==$t && $m==$this->_m && $y==$this->_y ){
        $class = "today";
      }
	  
	  /*
       * Build the opening and closing list item tags
       */
      $ls = sprintf("\n\t\t<li class=\"%s\">", $class);
      $le = "\n\t\t</li>";
	  
	  /*
       * Add the day of the month to identify the calendar box
       */
	  $event_info = NULL; // clear the variable
      if ( $this->_startDay<$i && $this->_daysInMonth>=$c){
        if ( isset($events[$c]) ){
          foreach ( $events[$c] as $event ){
            $link = '<a href="view.php?event_id='.$event->id .'">'.$event->title. '</a>';
            $event_info = "\n\t\t\t".$link;
          }
        }
        $date = sprintf("\n\t\t\t<strong>%02d</strong>",$c++);
      }
      else{
        $date="&nbsp;";
      }
	  
      /*
       * If the current day is a Saturday, wrap to the next row
       */
      $wrap = $i!=0 && $i%7==0 ? "\n\t</ul>\n\t<ul>" : NULL;
	  
      //Assemble the pieces into a finished item
      $html .= $ls . $date . $event_info . $le . $wrap;
    }
	
    //Add filler to finish out the last week
    while ( $i%7!=1 ){
      $html .= "\n\t\t<li class=\"fill\">&nbsp;</li>";
      ++$i;
    }
	
    //Close the final unordered list
    $html .= "\n\t</ul>\n\n";
    $admin = $this->_adminGeneralOptions();
    return $html.$admin;
  }
  
  public function displayEvent($id){
    /**
     * Make sure an ID was passed
     */
    if ( empty($id) ){
      return NULL;
    }
   
    /**
     * Make sure the ID is an integer
     */
    $id = preg_replace('/[^0-9]/', '', $id);
    
	/**
     * Load the event data from the DB
     */
    $event = $this->_loadEventById($id);
    
	/**
     * Generate strings for the date, start, and end time
     */
    $ts = strtotime($event->start);
    $date = date('F d, Y', $ts);
    $start = date('Y年m月d日 g:ia', $ts);
    $end = date('Y年m月d日 g:ia', strtotime($event->end));
    
	/*
     * Load admin options if the user is logged in
     */
    $admin = $this->_adminEntryOptions($id);
    return "<h2>$event->title</h2>\n\t<p class=\"dates\">$date, $start&mdash;$end</p>" ."\n\t<p>$event->description</p>$admin";
  }
  
  public function displayForm(){
    if ( isset($_POST['event_id'])){
      $id = (int) $_POST['event_id'];
      // Force integer type to sanitize data
    }
    else{
      $id = NULL;
    }
    
	/*
     * Instantiate the headline/submit button text
     */
    $submit = "添加新事件!";
    
	/*
     * If an ID is passed, loads the associated event
     */
    if ( !empty($id) ){
      $event = $this->_loadEventById($id);
      if ( !is_object($event) ){
        return NULL;
      }
      $submit = "编辑事件!";
    }
	
    return <<<FORM_MARKUP
      <form action="assets/inc/process.inc.php" method="post">
        <fieldset>
          <legend>$submit</legend>
          <label for="event_title">事件标题</label>
          <input type="text" name="event_title" id="event_title" value="$event->title" />
          <label for="event_start">开始时间</label>
          <input type="text" name="event_start" id="event_start" value="$event->start" />
          <label for="event_end">结束时间</label>
          <input type="text" name="event_end" id="event_end" value="$event->end" />
          <label for="event_description">事件描述</label>
          <textarea name="event_description" id="event_description">$event->description</textarea>
          <input type="hidden" name="event_id" value="$event->id" />
          <input type="hidden" name="token" value="$_SESSION[token]" />
          <input type="hidden" name="action" value="event_edit" />
		  <div class="section">
          	<input type="submit" name="event_submit" value="$submit" class="btn btn-primary"/>&nbsp;&nbsp;<a href="./" class = "cancel btn">取消</a>
		  </div>
        </fieldset>
      </form>
FORM_MARKUP;
  }

  public function processForm(){
    if ( $_POST['action']!='event_edit' ){
      return "The method processForm was accessed incorrectly";
    }
    $title = htmlentities($_POST['event_title'], ENT_QUOTES,"UTF-8");
    $desc = htmlentities($_POST['event_description'], ENT_QUOTES,"UTF-8");
    $start = htmlentities($_POST['event_start'], ENT_QUOTES,"UTF-8");
    $end = htmlentities($_POST['event_end'], ENT_QUOTES,"UTF-8");
    if(!$this->_validDate($start)||!$this->_validDate($end)){
      return "Invalid date format! Use YYYY-MM-DD HH:MM:SS.";
    }

    /*
     * If no event ID passed, create a new event
     */
    if ( empty($_POST['event_id']) ){
      $sql = "INSERT INTO `events` (`event_title`, `event_desc`, `event_start`, `event_end`)" ." VALUES (:title, :description, :start, :end)";
    }
    else{
      $id = (int) $_POST['event_id'];
      $sql = "UPDATE `events` SET `event_title`=:title,`event_desc`=:description,`event_start`=:start,`event_end`=:end WHERE `event_id`=$id";
    }
	
    try{
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(":title", $title, PDO::PARAM_STR);
      $stmt->bindParam(":description", $desc, PDO::PARAM_STR);
      $stmt->bindParam(":start", $start, PDO::PARAM_STR);
      $stmt->bindParam(":end", $end, PDO::PARAM_STR);
      $stmt->execute();
      $stmt->closeCursor();
      return true;
    }
    catch ( Exception $e ){
      return $e->getMessage();
    }
  }

  public function processAjaxForm(){
    if ( $_POST['action']!='event_edit' ){
      return "The method processForm was accessed incorrectly";
    }
    $title = htmlentities($_POST['event_title'], ENT_QUOTES,"UTF-8");
    $desc = htmlentities($_POST['event_description'], ENT_QUOTES,"UTF-8");
    $start = htmlentities($_POST['event_start'], ENT_QUOTES,"UTF-8");
    $end = htmlentities($_POST['event_end'], ENT_QUOTES,"UTF-8");
    if(!$this->_validDate($start)||!$this->_validDate($end)){
      return "Invalid date format! Use YYYY-MM-DD HH:MM:SS.";
    }

    /*
     * If no event ID passed, create a new event
     */
    if ( empty($_POST['event_id']) ){
      $sql = "INSERT INTO `events` (`event_title`, `event_desc`, `event_start`, `event_end`)" ." VALUES (:title, :description, :start, :end)";
    }
    else{
      $id = (int) $_POST['event_id'];
      $sql = "UPDATE `events` SET `event_title`=:title,`event_desc`=:description,`event_start`=:start,`event_end`=:end WHERE `event_id`=$id";
    }
	
    try{
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(":title", $title, PDO::PARAM_STR);
      $stmt->bindParam(":description", $desc, PDO::PARAM_STR);
      $stmt->bindParam(":start", $start, PDO::PARAM_STR);
      $stmt->bindParam(":end", $end, PDO::PARAM_STR);
      $stmt->execute();
      $stmt->closeCursor();
      //return true;
	  if(! empty($_POST['event_id']) ){
	  	return $_POST['event_id'];
	  }
	  else {
	  	return $this->db->lastInsertId();
	  }
    }
    catch ( Exception $e ){
      return $e->getMessage();
    }
  }
  
  public function confirmDelete($id){
    if ( empty($id) ) { return NULL; }
    $id = preg_replace('/[^0-9]/', '', $id);

    /*
     * If the confirmation form was submitted and the form.
     * has a valid token, check the form submission
     */
    if ( isset($_POST['confirm_delete'])&& $_POST['token']==$_SESSION['token'] ){
      /*
       * If the deletion is confirmed,
       * remove the event from the database
       */
      if ( $_POST['confirm_delete']=="删除" ){
        $sql = "DELETE FROM `events` WHERE `event_id`=:id LIMIT 1";
        try{
          $stmt = $this->db->prepare($sql);
          $stmt->bindParam(":id",$id,PDO::PARAM_INT);
          $stmt->execute();
          $stmt->closeCursor();
          header("Location: ./");
          return;
        }
		catch ( Exception $e ){
          return $e->getMessage();
        }
      }
	
      /*
       * If not confirmed,
       * sends the user to the main view
       */
      else{
        header("Location: ./");
        return;
      }
    }
  
    /**
     * If the confirmation form hasn't been submitted, display it
     */
    $event = $this->_loadEventById($id);

    /**
     * If no object is returned, return to the main view
     */
    if ( !is_object($event) ){
      header("Location: ./");
    }
    
	return <<<CONFIRM_DELETE
      <form action="confirmdelete.php" method="post">
        <h2>确定要删除 "$event->title" 吗?</h2>
        <p>删除后将<strong>不能恢复</strong>！</p>
        <p>
          <input type="submit" name="confirm_delete" value="删除" class="btn btn-primary"/>
          <input type="submit" name="confirm_delete" value="取消" class="btn"/>
          <input type="hidden" name="event_id" value="$event->id" />
          <input type="hidden" name="token" value="$_SESSION[token]" />
        </p>
      </form>
CONFIRM_DELETE;
  }
  
  private function _validDate($date){
    $pattern = '/^(\d{4}(-\d{2}){2} (\d{2})(:\d{2}){2})$/';
    /*
     * If a match is found, return TRUE. FALSE otherwise.
     */
    return preg_match($pattern, $date)==1 ? TRUE : FALSE;
  }
  
  /**
   * Loads event(s) info into an array
   *
   * @param int $id an optional event ID to filter results
   * @return array an array of events from the database
   */
  private function _loadEventData($id=NULL){
    $sql = "SELECT `event_id`, `event_title`, `event_desc`,`event_start`, `event_end` FROM `events`";
	
	/*
     * If an event ID is supplied, add a WHERE clause
     * so only that event is returned
     */
    if ( !empty($id) ){
      $sql .= "WHERE `event_id`=:id LIMIT 1";
    }
	/*
     * Otherwise, load all events for the month in use
     */
	else{
	  /*
       * Find the first and last days of the month
       */
      $start_ts = mktime(0, 0, 0, $this->_m, 1, $this->_y);
      $end_ts = mktime(23, 59, 59, $this->_m+1, 0, $this->_y);
      $start_date = date('Y-m-d H:i:s', $start_ts);
      $end_date = date('Y-m-d H:i:s', $end_ts);
	  
	  /*
       * Filter events to only those happening in the
       * currently selected month
       */
      $sql .= "WHERE `event_start` BETWEEN '$start_date' AND '$end_date' ORDER BY `event_start`";
    }
	
    try{
      $stmt = $this->db->prepare($sql);

      /*
       * Bind the parameter if an ID was passed
       */
      if ( !empty($id) ){
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
      }
      $stmt->execute();
      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
      return $results;
    }
    catch ( Exception $e ){
      die ( $e->getMessage() );
    }
  }
  
  private function _createEventObj(){
    /* *
     * Load the events array
     */
    $arr = $this->_loadEventData();
    
	/**
     * Create a new array, then organize the events* by the day of the monthon which they occur
     */
    $events = array();
    foreach ( $arr as $event ){
      $day = date('j', strtotime($event['event_start']));
      try{
        $events[$day][] = new Event($event);
      }
      catch ( Exception $e ){
        die ( $e->getMessage() );
      }
    }
    return $events;
  }
  
  private function _loadEventById($id){
    if ( empty($id) ){
      return NULL;
    }
    $event = $this->_loadEventData($id);
    if ( isset($event[0]) ){
      return new Event($event[0]);
    }
    else{
      return NULL;
    }
  }
  
  private function _adminGeneralOptions(){
    if ( isset($_SESSION['user']) ){
      return <<<ADMIN_OPTIONS
        <div class="section"><a href="admin.php" class="admin btn btn-primary">+ 新&nbsp;建&nbsp;活&nbsp;动</a></div>
        <form action="assets/inc/process.inc.php" method="post">
          <div>
            <input type="submit" value="登&nbsp;&nbsp;出" class="btn btn-primary" />
            <input type="hidden" name="token" value="$_SESSION[token]" />
            <input type="hidden" name="action" value="user_logout" />
          </div>
        </form>
ADMIN_OPTIONS;
    }
    else{
      return <<<ADMIN_OPTIONS
        <div class="section"><a href="login.php" class="btn btn-primary loginAsAjax">登&nbsp;&nbsp;录</a></div>
ADMIN_OPTIONS;
    }
  }
  
  private function _adminEntryOptions($id){
    if ( isset($_SESSION['user']) ){
      return <<<ADMIN_OPTIONS
	  	<div class="section">
			<div class="admin-options">
			  <form action="admin.php" method="post">
				<p>
				  <input type="submit" name="edit_event" value="编&nbsp;&nbsp;辑" class="btn btn-primary" />
				  <input type="hidden" name="event_id" value="$id" />
				</p>
			  </form>
			  <form action="confirmdelete.php" method="post">
				<p>
				  <input type="submit" name="delete_event" value="删&nbsp;&nbsp;除" class="btn"/>
				  <input type="hidden" name="event_id" value="$id" />
				</p>
			  </form>
			</div>
		</div>
<!-- end .admin-options -->
ADMIN_OPTIONS;
    }
    else{
      return NULL;
    }
  }
}
?>