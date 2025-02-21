<?php

namespace App\Domain\Model\Event\Repository;

use App\common\CurrentOrganizer;
use App\common\CurrentUser;
use App\common\Repository\BaseRepository;
use App\Domain\Model\Event\Event;
use Exception;
use PDO;
use PDOException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;

class EventRepository extends BaseRepository
{


    /**
     * Constructor.
     *
     * @param PDO $connection The database connection
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }



    public function allEvents(): array
    {

        try {
            $statement = $this->connection->prepare($this->sqls('allEvents'));
            $statement->execute();
            $events = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Event\Event::class, null);

            if (empty($events)) {
                return array();
            }

            return $events;
        } catch (PDOException $e) {
            error_log("Error: " . $e->getMessage());
        }
        return array();
    }

    public function eventFor(string $event_uid): ?Event
    {
        try {
            $statement = $this->connection->prepare($this->sqls('getEventByUid'));
            $statement->bindParam(':event_uid', $event_uid);
            $statement->execute();
            $events = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Event::class, null);
            
            return empty($events) ? null : $events[0];
        } catch (PDOException $e) {
            error_log("Error fetching event: " . $e->getMessage());
            throw $e; // Rethrow to allow proper error handling upstream
        }
    }


    public function eventsForOrganizer(string $organizer_id): array
    {
        try {
            $statement = $this->connection->prepare($this->sqls('eventsForOrganizer'));
            $statement->bindParam(':organizer_id', $organizer_id);
            $statement->execute();
            $events = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Event::class, null);
            
            return empty($events) ? array() : $events;
        } catch (PDOException $e) {
            error_log("Error: " . $e->getMessage());
            return array();
        }
    }


    public function tracksOnEvent(string $event_uid): ?array
    {
        try {
            return $this->executeQuery($this->sqls('tracksOnEvent'), ['event_uid' => $event_uid]);
        } catch (PDOException $e) {
            error_log("Error: " . $e->getMessage());
            return array();
        }
    }


    public function trackAndEventOnEvent(string $event_uid, string $track_uid): ?array
    {
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
        } catch (PDOException $e) {
            error_log("Error: " . $e->getMessage());
        }
        return array();
    }

    public function updateEvent(string $event_uid, Event $event): Event
    {

        $title = $event->getTitle();
        $description = $event->getDescription();
        $completed = $event->isCompleted();
        $active = $event->isActive();
        $canceled = $event->isCanceled();
        $start_date = $event->getStartdate();
        $end_date = $event->getEnddate();
        $eve_U = $event->getEventUid();
        $organizer_id = $this->getOrganizer();
        try {
            $statement = $this->connection->prepare($this->sqls('updateEvent'));

            $statement->bindValue(':event_uid', $eve_U);
            $statement->bindValue(':organizer_id', $organizer_id);
            $statement->bindValue(':title', $title);
            $statement->bindValue(':description', $description);
            $statement->bindValue(':completed', $completed, PDO::PARAM_BOOL);
            $statement->bindValue(':active', $active, PDO::PARAM_BOOL);
            $statement->bindValue(':canceled', $canceled, PDO::PARAM_BOOL);
            $statement->bindValue(':end_date', $end_date);
            $statement->bindValue(':start_date', $start_date);


            $status = $statement->execute();
            if ($status) {
                return $event;
            }

        } catch (PDOException $e) {
            error_log('Could not update site: ' . $e->getMessage());
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
        $organizer_id = $this->getOrganizer();
    
        try {
            $statement = $this->connection->prepare($this->sqls('createEvent'));

            $statement->bindParam(':event_uid', $event_uid);
            $statement->bindParam(':organizer_id', $organizer_id);
            $statement->bindParam(':title', $title);
            $statement->bindParam(':description', $description);
            $statement->bindParam(':completed', $completed, PDO::PARAM_BOOL);
            $statement->bindParam(':active', $active, PDO::PARAM_BOOL);
            $statement->bindParam(':canceled', $canceled, PDO::PARAM_BOOL);
            $statement->bindParam(':end_date', $end_date);
            $statement->bindParam(':start_date', $start_date);
            $status = $statement->execute();


            if ($status) {
                $event->setEventUid($event_uid);
                return $event;
            }
        } catch (PDOException $e) {
            error_log('Could not update site: ' . $e->getMessage());
        }
        $event->setEventUid($event_uid);
        return $event;
    }

    public function existsByTitleAndStartDate(string $title, $start_date): ?Event
    {
        $statement = $this->connection->prepare($this->sqls('existsByTitleAndStartDate'));
        $statement->bindParam(':title', $title);
        $statement->execute();
        $event = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Event\Event::class, null);
        if (!empty($event)) {
            return $event[0];
        }

        return null;
        //   $statement->bindParam(':start_date', $start_date);
    }


    public function createTrackEvent($event_uid, $track_uids)
    {

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
        } catch (PDOException $e) {
            error_log("Error: " . $e->getMessage());
        }
    }

    public function sqls($type): string
    {
        $eventqls['tracksOnEvent'] = 'select track_uid  from event_tracks e where e.event_uid=:event_uid;';
        $eventqls['trackAndEventOnEvent'] = 'select track_uid  from event_tracks where track_uid=:track_uid and event_uid=:event_uid;';
        $eventqls['allEvents'] = 'select * from event e;';
        $eventqls['getEventByUid'] = 'select *  from event e where e.event_uid=:event_uid;';
        $eventqls['deleteEvent'] = 'delete from event  where event_uid=:event_uid;';
        $eventqls['updateEvent'] = "UPDATE event SET organizer_id=:organizer_id, title=:title, description=:description, active=:active, completed=:completed, canceled=:canceled, start_date=:start_date, end_date=:end_date WHERE event_uid=:event_uid";
        $eventqls['createEvent'] = "INSERT INTO event(event_uid, organizer_id, title, start_date, end_date, active, canceled, completed, description) VALUES (:event_uid, :organizer_id, :title, :start_date, :end_date, :active, :canceled, :completed, :description)";
        $eventqls['createEventTrack'] = 'INSERT INTO event_tracks(track_uid, event_uid) VALUES (:track_uid , :event_uid)';
        $eventqls['existsByTitleAndStartDate'] = 'select *  from event e where e.title=:title;';
        $eventqls['eventsForOrganizer'] = 'select *  from event e where e.organizer_id=:organizer_id;';




        return $eventqls[$type];

    }


}