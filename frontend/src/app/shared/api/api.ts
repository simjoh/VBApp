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
  id: string;
  givenname: string;
  familyname: string;
  username: string;
  token?: string;
  roles?: [];
}
