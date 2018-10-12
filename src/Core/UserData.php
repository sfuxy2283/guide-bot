<?php

namespace Linebot\Core;

use Linebot\Exceptions\DbException;
use \PDO;

class UserData
{
    /**
     * The id of line user
     * 
     * @var string
     */
    private $lineUserId;
    
    /**
     * The array has user data
     * 
     * @var array
     */
    private $userData;

    /**
     * Database information
     *
     * @var querystring
     */
    private $db;

    /**
     * The array has location data
     *
     * @var array
     */
    private $locationData;

    /**
     * Class constructor
     *
     * @param $lineUserId
     * @throws DbException
     */
    public function __construct($lineUserId)
    {
        $dbHost = getenv("DB_HOST");
        $dbName = getenv("DB_NAME");
        
        $this->db = new PDO(
            "mysql:{$dbHost};dbname={$dbName}",
            getenv("DB_USERNAME"),
            getenv("DB_PASSWORD")
        );

        $this->lineUserId = $lineUserId;

        $this->userData = $this->getUserData();
    }

    /**
     * Get the user data from the database, if there is no data about the user, set data and return it
     *
     * @return void
     * @throws DbException
     */
    public function getUserData()
    {
        $query = 'SELECT * FROM users WHERE user_id = :id';

        $sth = $this->db->prepare($query);
        $sth->bindValue(':id', $this->lineUserId, PDO::PARAM_STR);

        if (!$sth->execute()) {

            throw new DbException($sth->errorInfo()[2]);
        }

        $userData = $sth->fetch(PDO::FETCH_ASSOC);

        if (empty($userData)) {

           $this->setUser();

           $this->getUserData();
        }

        return $userData;
    }

    /**
     * Set user data into database
     *
     * @return void
     * @throws DbException
     */
    public function setUser()
    {
        $query = 'INSERT INTO users (user_id) VALUES (:id)';

        $sth = $this->db->prepare($query);
        $sth->bindValue(':id', $this->lineUserId, PDO::PARAM_STR);

        if (!$sth->execute()) {

            throw new DbException($sth->errorInfo()[2]);
        }
    }

    /**
     * Get the user data from the database, if there is no data about the user, set data and return it
     *
     * @return the location data of user
     * @throws DbException
     */
    public function getLocationData()
    {
        $query = 'SELECT * FROM locations WHERE user_id = :id';

        $sth = $this->db->prepare($query);
        $sth->bindValue(':id', $this->lineUserId, PDO::PARAM_STR);

        if (!$sth->execute()) {

            throw new \Exception("getlocation error");
        }

        $locationData = $sth->fetch(PDO::FETCH_ASSOC);

        return $locationData;
    }

    /**
     * Set location data into database
     *
     * @return void
     * @throws DbException
     */
    public function setLocationData($locationData)
    {
        $query = 'INSERT INTO locations (user_id, title, address, latitude, longitude) VALUES (:id, :title, :address, :lat, :long)';

        $sth = $this->db->prepare($query);
        $sth->bindValue(':id', $this->lineUserId, PDO::PARAM_STR);
        $sth->bindValue(':title', $locationData['title'], PDO::PARAM_STR);
        $sth->bindValue(':address', $locationData['address'], PDO::PARAM_STR);
        $sth->bindValue(':lat', $locationData['latitude'], PDO::PARAM_STR);
        $sth->bindValue(':long', $locationData['longitude'], PDO::PARAM_STR);

        if (!$sth->execute()) {

            throw new \Exception("set location error");
        }
    }

    public function deleteLocationData()
    {
        $query = 'DELETE FROM locations WHERE user_id = :id';

        $sth = $this->db->prepare($query);
        $sth->bindValue(':id', $this->lineUserId, PDO::PARAM_STR);

        if (!$sth->execute()) {

            throw new \Exception("delete location error");
        }
    }

    /**
     * Turn on the bot mode
     *
     * @param string $modeName The name of mode that want to turn on
     * @throws DbException
     */
    public function modeTurnOn($modeName)
    {
        $query = "UPDATE users SET $modeName = true WHERE user_id = :id";

        $sth = $this->db->prepare($query);
        $sth->bindValue(':id', $this->lineUserId, PDO::PARAM_STR);

        if (!$sth->execute()) {

            throw new DbException($sth->errorInfo()[2]);
        }

    }

    /**
     * Turn off the bot mode
     *
     * @param string $modeName The name of mode that want to turn off
     * @throws DbException
     */
    public function modeTurnOff($modeName)
    {
        $query = "UPDATE users SET $modeName = false WHERE user_id = :id";

        $sth = $this->db->prepare($query);
        $sth->bindValue(':id', $this->lineUserId, PDO::PARAM_STR);

        if (!$sth->execute()) {

            throw new DbException($sth->errorInfo()[2]);
        }

    }

    /**
     * Return user id
     * 
     * @return string user id
     */
    public function getUserId()
    {
        return $this->lineUserId;
    }

    /**
     * Return current status of translate mode
     * 
     * @return boolean 
     */
    public function isTranslateMode()
    {
        return $this->userData['translate'];
    }

    /**
     * Return current status of echo mode
     * 
     * @return boolean
     */
    public function isEchoMode()
    {
        return $this->userData['echo'];
    }

    /**
     * Return current status of place mode
     *
     * @return boolean
     */
    public function isPlaceMode()
    {
        return $this->userData['place'];
    }

    /**
     * Return current status of weather mode
     *
     * @return boolean
     */
    public function isWeatherMode()
    {
        return $this->userData['weather'];
    }


}