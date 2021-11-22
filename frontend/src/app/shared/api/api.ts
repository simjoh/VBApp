export interface Link {
  rel?: string;
  method?: string;
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
  link?: Link;
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
