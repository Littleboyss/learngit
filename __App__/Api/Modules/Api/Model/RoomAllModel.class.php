<?php
class RoomAllModel extends ViewModel {
   public $viewFields = array(
     'match_room'=>array('*'),
     'match_room_info'=>array('match_team','_on'=>'match_room.id=match_room_info.room_id'),
   );
 }