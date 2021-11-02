export enum EventType {
  Error,
  Login
}

export const eventGroup = {
  error: [EventType.Error],
  login: [EventType.Login]
};

export class EventInformation {
  typ: string | undefined;
  message: unknown;
}

export class AEvent {
  constructor(public type: EventType, public data: unknown) {

  }
}
