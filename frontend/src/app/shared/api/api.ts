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
  check_in_distance?: string;
  links?: Link[];
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
  links: Link[];
}




export interface User {
  user_uid: string;
  givenname: string;
  familyname: string;
  username: string;
  token: string;
  roles: string[];
  userInfoRepresentation: UserInfoRepresentation;
  organizer_id?: number;
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
  links: Link[];
}

export interface RandonneurCheckPointRepresentation {
  checkpoint: CheckpointRepresentation;
  active: boolean;
  stamptime: string;
  checkouttime: string;
  links: Link[];
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
  check_in_distance: string;
  links: Link[];
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
  has_checkouted: boolean;
  volonteer_checkin: boolean;
  dnf: boolean;
  link: Link[];
}

export interface TrackRepresentation {
  title: string;
  descriptions: string;
  checkpoints: any[];
  linktotrack: string;
  heightdifference: string;
  distance: string;
  track_uid: string;
  event_uid: string;
  start_date_time: string;
  active: number;
  links: Link[];
}

export interface EventInformationRepresentation {
  event: EventRepresentation
  tracks: TrackRepresentation[]
}

export interface  TrackMetricsRepresentation{
  countParticipants:string;
  countDnf:string
  countDns: string;
  countFinished: string;
}


export interface ParticipantRepresentation {
  participant_uid: string;
  track_uid: string;
  competitor_uid: string;
  startnumber: string ;
  finished: boolean;
  acpcode: string;
  club_uid: string;
  time: string;
  dns: boolean;
  dnf: boolean;
  started: boolean;
  brevenr: string;
  dns_timestamp?: string;
  dnf_timestamp?: string;
  links: Link[];
}

export interface CompetitorRepresentation {
  given_name: string;
  family_name: string;
  birth_date: string;
  links: [];
}

export interface ParticipantInformationRepresentation {
  participant: ParticipantRepresentation ;
  competitorRepresentation: CompetitorRepresentation

}


export interface ClubRepresentation {
  club_uid: string;
  title: string;
  acp_kod: string;
  links: Link[];
}




