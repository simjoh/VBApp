export enum EventType {
  Error,
}

export const eventGroup = {
  error: [EventType.Error]
};

export class EventInformation {
  typ: string | undefined;
  message: unknown;
}

export class Event {
  constructor(public type: EventType, public data: unknown) {

  }
}
