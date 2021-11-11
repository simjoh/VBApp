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
    }

    public function allEvents(): array
    {
        try {
            $statement = $this->connection->prepare($this->sqls('allEvents'));
            $statement->execute();
            $events = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,  \App\Domain\Model\Event\Event::class, null);

            if (empty($events)) {
                return array();
            }

            return $events;
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
        return array();
    }

    public function eventFor(string $event_uid): Event
    {
        try {

            $statement = $this->connection->prepare($this->sqls('getEventByUid'));
            $statement->bindParam(':event_uid', $event_uid);
            $statement->execute();
            $event = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Event\Event::class, null);
            if($statement->rowCount() > 1){
                // Fixa bätter felhantering
                throw new Exception();
            }
            if(!empty($event)){
                return $event[0];
            }
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
        return new $event;
    }

    public function updateEvent(string $event_uid , Event $event): Event
    {
        $event_uid = $event->getEventUid();
        $title = $event->getTitle();
        $description = $event->getDescription();
        $completed = $event->isCompleted();
        $active = $event->isActive();
        $canceled = $event->isCanceled();
        $start_date = $event->getStartdate();
        $end_date = $event->getEnddate();
        try {
            $statement = $this->connection->prepare($this->sqls('updateEvent'));
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
                return $event;
            }
        } catch (PDOException $e) {
            echo 'Kunde inte uppdatera site: ' . $e->getMessage();
        }
        return $event;
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
        $eventqls['allEvents'] = 'select * from event e;';
        $eventqls['getEventByUid'] = 'select *  from event e where e.event_uid=:event_uid;';
        $eventqls['deleteEvent'] = 'delete from event e where e.event_uid=:event_uid;';
        $eventqls['updateEvent']  = "UPDATE event SET  title=:title ,description=:description , active=:active, completed=:completed, canceled=:canceled, active=:active , start_date=:start_date, end_date=:end_date WHERE event_uid=:event_uid";
        $eventqls['createEvent']  = "INSERT INTO event(event_uid, title, start_date, end_date, active, canceled, completed,description) VALUES (:event_uid, :title,:start_date,:end_date,:active, :canceled, :completed, :description)";
        return $eventqls[$type];
    }
}