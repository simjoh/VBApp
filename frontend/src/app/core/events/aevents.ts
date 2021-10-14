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

export class AEvent {
  constructor(public type: EventType, public data: unknown) {

  }
}
