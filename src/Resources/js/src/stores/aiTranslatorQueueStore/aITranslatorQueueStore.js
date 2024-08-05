import { observable, action } from "mobx";

class AITranslatorQueueStore {
    @observable activeItemsLength = 0;
    @observable totalItemsLength = 0;
    @observable bulkTranslateInProgress = false;

    @action
    setBulkTranslateInProgress(inProgress = false) {
        this.bulkTranslateInProgress = inProgress;
    }

    @action
    resetQueue() {
        this.activeItemsLength = 0;
        this.totalItemsLength = 0;
        this.bulkTranslateInProgress = false;
    }

    @action
    removeActiveQueueItem() {
        this.activeItemsLength--;
    }

    @action
    addActiveQueueItem() {
        this.activeItemsLength++;
        this.totalItemsLength++;
    }
}

export default new AITranslatorQueueStore();
