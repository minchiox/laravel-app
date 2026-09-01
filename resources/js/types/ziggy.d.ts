import { route as routeFn } from 'ziggy-js';

declare global {
    // eslint-disable-next-line no-var
    var route: typeof routeFn;
}
