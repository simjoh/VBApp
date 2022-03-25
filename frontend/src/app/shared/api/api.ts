export interface Link {
  rel?: string;
  method?: string;
  url?:string
}

export interface Site{
  site_uid: string;
  place?: string;
  adress?: string;
  location?: string;
  image?: string;
  description?: string;
  lat?: string;
  lng?: string;
  picture?: string;
  link?: Link;
}

export interface Event {
  event_uid: string;
  title: string;
  startdate: string;
  enddate: string;
  active: boolean;
  canceled: boolean;
  completed: boolean;
  description: string;
  links: [];
}




export interface User {
  user_uid: string;
  givenname: string;
  familyname: string;
  username: string;
  token: string;
  roles: string[];
  userInfoRepresentation: UserInfoRepresentation;
}

export interface UserRepresentation {
  user_uid: string;
  givenname: string;
  familyname: string;
  username: string;
  token: string;
  roles: string[];
}

export interface UserInfoRepresentation {
  user_uid: string;
  uid: string;
  phone: string;
  email: string;
  link?: Link;
}

export interface Role {
  id: number;
  role_name: string;
}

export interface Role {
  id: number;
  role_name: string;
}

export interface EventRepresentation {
  event_uid: string;
  title: string;
  startdate: any;
  enddate: any;
  active: boolean;
  canceled: boolean;
  completed: boolean;
  description: string;
  links: [];
}

export interface RandonneurCheckPointRepresentation {
  checkpoint: CheckpointRepresentation;
  links: [];
}

export interface  SiteRepresentation {
  site_uid: string;
  place: string;
  adress: string;
  location: any;
  image: string;
  description: string;
  lat: string;
  lng: string;
  links: [];
}

export interface CheckpointRepresentation {
  checkpoint_uid: string;
  title: string;
  site:  SiteRepresentation;
  description: string;
  distance: any;
  opens: any;
  closing: any;
  link: Link;
}

export interface ParticipantToPassCheckpointRepresentation {
  trackUid: string;
  participantUid: string
  siteUid: string;
  checkpointUid: string
  adress: string;
  startNumber: string
  givenName: string;
  familyName: string;
  passed: boolean;
  passededDateTime?: string;
  dnf: boolean;
  link: [];
}

