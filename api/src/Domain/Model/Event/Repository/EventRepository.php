<?php

namespace App\Domain\Model\Event\Repository;

use App\common\Repository\BaseRepository;
use App\Domain\Model\Event\Event;
use Exception;
use PDO;
use PDOException;
use Ramsey\Uuid\Uuid;

class EventRepository extends BaseRepository
{

    /**
     * Constructor.
     *
     * @param PDO $connection The database connection
     */
    public function __construct(PDO $connection) {
        $this->connection = $connection;
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function allEvents(): array
    {
        $startTime = microtime(true);
        error_log("EventRepository::allEvents START");
        
        try {
            $queryStart = microtime(true);
            $statement = $this->connection->prepare($this->sqls('allEvents'));
            $statement->execute();
            $events = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,  \App\Domain\Model\Event\Event::class, null);
            $queryTime = microtime(true) - $queryStart;
            error_log("EventRepository::allEvents - SQL query took: " . number_format($queryTime * 1000, 2) . "ms, returned " . count($events) . " events");

            if (empty($events)) {
                error_log("EventRepository::allEvents END - No events found, total time: " . number_format((microtime(true) - $startTime) * 1000, 2) . "ms");
                return array();
            }

            $totalTime = microtime(true) - $startTime;
            error_log("EventRepository::allEvents END - Total time: " . number_format($totalTime * 1000, 2) . "ms");
            return $events;
        }
        catch(PDOException $e)
        {
            error_log("EventRepository::allEvents ERROR: " . $e->getMessage());
            echo "Error: " . $e->getMessage();
        }
        return array();
    }

    public function eventFor(string $event_uid): ?Event
    {
        $startTime = microtime(true);
        error_log("EventRepository::eventFor START - event_uid: $event_uid");
        
        try {
            $queryStart = microtime(true);
            $statement = $this->connection->prepare($this->sqls('getEventByUid'));
            $statement->bindParam(':event_uid', $event_uid);
            $statement->execute();
            $event = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Event\Event::class, null);
            $queryTime = microtime(true) - $queryStart;
            error_log("EventRepository::eventFor - SQL query took: " . number_format($queryTime * 1000, 2) . "ms, returned " . count($event) . " events");

            if($statement->rowCount() > 1){
                // Fixa bätter felhantering
                throw new Exception();
            }
            if(!empty($event)){
                $totalTime = microtime(true) - $startTime;
                error_log("EventRepository::eventFor END - Event found, total time: " . number_format($totalTime * 1000, 2) . "ms");
                return $event[0];
            }
        }
        catch(PDOException $e)
        {
            error_log("EventRepository::eventFor ERROR: " . $e->getMessage());
            echo "Error: " . $e->getMessage();
        }

        $totalTime = microtime(true) - $startTime;
        error_log("EventRepository::eventFor END - No event found, total time: " . number_format($totalTime * 1000, 2) . "ms");
        return null;
    }

    public function getEvent(string $event_uid): ?Event
    {
        return $this->eventFor($event_uid);
    }

    public function tracksOnEvent(string $event_uid): ?array {
        try {
            $statement = $this->connection->prepare($this->sqls('tracksOnEvent'));
            $statement->bindValue(':event_uid', $event_uid);
            $statement->execute();
            $track_uids = $statement->fetchAll();
            if (empty($track_uids)) {
                return array();
            }

            return $track_uids;
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
        return array();
    }



    public function trackAndEventOnEvent(string $event_uid, string $track_uid): ?array {
        try {

            $statement = $this->connection->prepare($this->sqls('trackAndEventOnEvent'));
            $statement->bindValue(':event_uid', $event_uid);
            $statement->bindValue(':track_uid', $track_uid);
            $statement->execute();
            $track_uids = $statement->fetchAll();
            if (empty($track_uids)) {
                return array();
            }

            return $track_uids;
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
        return array();
    }

    public function updateEvent(string $event_uid , Event $event): Event
    {
        $title = $event->getTitle();
        $description = $event->getDescription();
        $completed = $event->isCompleted();
        $active = $event->isActive();
        $canceled = $event->isCanceled();
        $start_date = $event->getStartdate();
        $end_date = $event->getEnddate();
        $eve_U = $event->getEventUid();
        
        try {
            $statement = $this->connection->prepare($this->sqls('updateEvent'));

            $statement->bindValue(':event_uid', $eve_U);
            $statement->bindValue(':title', $title);
            $statement->bindValue(':description', $description);
            $statement->bindValue(':completed',$completed ,PDO::PARAM_BOOL);
            $statement->bindValue(':active', $active,PDO::PARAM_BOOL);
            $statement->bindValue(':canceled', $canceled, PDO::PARAM_BOOL);
            $statement->bindValue(':end_date', $end_date);
            $statement->bindValue(':start_date', $start_date);

            $status = $statement->execute();
            if($status){
                return $event;
            }

        } catch (PDOException $e) {
            echo 'Kunde inte uppdatera site: ' . $e->getMessage();
        }
        return $event;
    }

    public function createEvent(Event $event): Event
    {
        $event_uid = Uuid::uuid4();
        $title = $event->getTitle();
        $description = $event->getDescription();
        $completed = $event->isCompleted();
        $active = $event->isActive();
        $canceled = $event->isCanceled();
        $start_date = $event->getStartdate();
        $end_date = $event->getEnddate();
        
        try {
            $statement = $this->connection->prepare($this->sqls('createEvent'));

            $statement->bindParam(':event_uid', $event_uid);
            $statement->bindParam(':title', $title);
            $statement->bindParam(':description', $description);
            $statement->bindParam(':completed',$completed ,PDO::PARAM_BOOL);
            $statement->bindParam(':active', $active,PDO::PARAM_BOOL);
            $statement->bindParam(':canceled', $canceled, PDO::PARAM_BOOL);
            $statement->bindParam(':end_date', $end_date);
            $statement->bindParam(':start_date', $start_date);
            
            $status = $statement->execute();

            if($status){
                $event->setEventUid($event_uid);
                return $event;
            }
        } catch (PDOException $e) {
            echo 'Kunde inte uppdatera site: ' . $e->getMessage();
        }
        $event->setEventUid($event_uid);
        return $event;
    }

    public function existsByTitleAndStartDate(string $title,  $start_date): ?Event
    {
        $statement = $this->connection->prepare($this->sqls('existsByTitleAndStartDate'));
        $statement->bindParam(':title', $title);
         $statement->execute();
        $event = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Event\Event::class, null);
        if(!empty($event)){
            return $event[0];
        }

        return null;
     //   $statement->bindParam(':start_date', $start_date);
    }


    public function createTrackEvent($event_uid, $track_uids){

        $statement = $this->connection->prepare($this->sqls('createEventTrack'));
//        foreach ($track_uids as $value) {
            $statement->bindParam(':event_uid', $event_uid);
            $statement->bindParam(':track_uid', $track_uids);
            $statement->execute();
//        }
    }

    public function deleteEvent(string $event_uid)
    {
        try {
            $stmt = $this->connection->prepare($this->sqls('deleteEvent'));
            $stmt->bindParam(':event_uid', $event_uid);
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
    }
    public function sqls($type): string
    {
        $eventqls['tracksOnEvent'] = 'select track_uid  from event_tracks e where e.event_uid=:event_uid;';
        $eventqls['trackAndEventOnEvent'] = 'select track_uid  from event_tracks where track_uid=:track_uid and event_uid=:event_uid;';
        $eventqls['allEvents'] = 'select * from event e;';
        $eventqls['getEventByUid'] = 'select *  from event e where e.event_uid=:event_uid;';
        $eventqls['deleteEvent'] = 'delete from event  where event_uid=:event_uid;';
        $eventqls['updateEvent']  = "UPDATE event SET  title=:title , description=:description , active=:active, completed=:completed, canceled=:canceled, active=:active , start_date=:start_date, end_date=:end_date WHERE event_uid=:event_uid";
        $eventqls['createEvent']  = "INSERT INTO event(event_uid, title, start_date, end_date, active, canceled, completed, description) VALUES (:event_uid, :title, :start_date, :end_date, :active, :canceled, :completed, :description)";
        $eventqls['createEventTrack'] = 'INSERT INTO event_tracks(track_uid, event_uid) VALUES (:track_uid , :event_uid)';
        $eventqls['existsByTitleAndStartDate'] = 'select *  from event e where e.title=:title;';


        return $eventqls[$type];

    }

    /**
     * Get the database connection
     * 
     * @return PDO The database connection
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

}