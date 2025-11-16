import { TokenPayload } from '../config/auth';

export type Variables = {
  user: TokenPayload;
};

export type AppContext = {
  Variables: Variables;
};