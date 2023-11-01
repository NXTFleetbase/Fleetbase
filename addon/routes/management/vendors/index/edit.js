import Route from '@ember/routing/route';
import { inject as service } from '@ember/service';

export default class ManagementVendorsIndexEditRoute extends Route {
    @service store;

    model({ public_id }) {
        return this.store.findRecord('vendor', public_id);
    }
}
