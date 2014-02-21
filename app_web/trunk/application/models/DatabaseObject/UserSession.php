<?
class DatabaseObject_UserSession extends DatabaseObject{

    public function __construct($db)
    {
        parent::__construct($db,'users_sessions','user_id');
        $this->add('user_id');
        $this->add('sessionId');
        $this->add('session_type',0);
        $this->add('device_id',NULL);
        $this->add('device_version',NULL);
        $this->add('device_token',NULL);
        $this->add('version',NULL);
        $this->add('lat',NULL);
        $this->add('lon',NULL);
        $this->add('tsCreated',time());
        $this->add('tsModified',NULL);
    }


    public function loadForUser($user_id, $sessionId)
    {
        $user_id   = (int) $user_id;
        $sessionId = $sessionId;

        if ($user_id <= 0 || length($sessionId) <= 0)
            return false;

        $query = sprintf(
            'select %s from %s where user_id = %d and session_id = %s',
            join(', ', $this->getSelectFields()),
            $this->_table,
            $user_id,
            $sessionId
        );

        return $this->_load($query);
    }

}
