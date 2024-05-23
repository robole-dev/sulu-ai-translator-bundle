import { translate } from "sulu-admin-bundle/utils";
import { AbstractListToolbarAction } from "sulu-admin-bundle/views";
import { reaction } from "mobx";
import translateQueueStore from "../../stores/aiTranslatorQueueStore/aITranslatorQueueStore";

const clickEvent = new Event("click", { bubbles: true });

export default class AITranslatorToolbarAction extends AbstractListToolbarAction {
  buttonRef = undefined;
  disposer = null;

  constructor(props) {
    super(props);

    this.disposer = reaction(
      () => [
        translateQueueStore.activeItemsLength,
        translateQueueStore.totalItemsLength,
      ],
      () => {
        this.updateState(
          translateQueueStore.activeItemsLength,
          translateQueueStore.totalItemsLength
        );
      }
    );
  }

  componentWillUnmount() {
    if (this.disposer) {
      this.disposer();
    }
  }

  updateState(activeItemsLength, totalItemsLength) {
    if (!this.buttonRef || totalItemsLength === 0) {
      return;
    }

    this.setButtonLabelSpan(
      `${translate("app.translator_bulk_translate")} (${
        totalItemsLength - activeItemsLength
      }/${totalItemsLength})`
    );

    this.setButtonProgress(
      parseInt(
        ((totalItemsLength - activeItemsLength) / totalItemsLength) * 100
      )
    );

    if (activeItemsLength === 0) {
      // Bulk translation completed!
      this.resetDefaultButtonStyle();
      translateQueueStore.resetQueue();
    }
  }

  setButtonLabelSpan(text = "") {
    const labelSpan = this.buttonRef.querySelectorAll("span")[1];

    if (labelSpan) {
      labelSpan.innerText = text;
    }
  }

  setButtonProgress(progress = 0) {
    if (!this.buttonRef) {
      return;
    }

    this.buttonRef.style.setProperty("--translator-process", `${progress}%`);
  }

  resetDefaultButtonStyle() {
    this.setButtonProgress(0);
    this.setButtonLabelSpan(translate("app.translator_bulk_translate"));
  }

  getToolbarItemConfig() {
    return {
      type: "button",
      label: translate("app.translator_bulk_translate"),
      icon: "su-language",
      disabled: false,
      showText: true,
      onClick: this.handleClick,
      buttonRef: (ref) => {
        this.buttonRef = ref;
        this.buttonRef.classList.add("translator__bulk-btn");
      },
    };
  }

  handleClick = () => {
    // @todo Use sulu-modal-confirm component
    if (
      translateQueueStore.bulkTranslateInProgress ||
      confirm(translate("app.translator_bulk_translate_confirm")) === false
    ) {
      return;
    }

    this.toggleBlocks();

    // @todo A better implementation would wait for all blocks to be opened.
    // This could be achieved by a ToggleBlockManager service.
    window.setTimeout(() => {
      translateQueueStore.setBulkTranslateInProgress(true);
      this.buttonRef.classList.add("translator__bulk");
    }, 200);
  };

  toggleBlocks($root = document) {
    const $sortableBlockListItems = $root.querySelectorAll(
      "main div[class*=' sortableBlockList--'] section, main div[class^='sortableBlockList--'] section"
    );

    [...$sortableBlockListItems].forEach((item) => {
      item.dispatchEvent(clickEvent);

      // Recursively open all child blocks (if any)
      window.requestAnimationFrame(() => {
        this.toggleBlocks(item);
      });
    });
  }
}
