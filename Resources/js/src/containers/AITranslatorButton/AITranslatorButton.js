import React from "react";
import { observer } from "mobx-react";
import { observable, action, reaction } from "mobx";
import classNames from "classnames";
import { Requester } from "sulu-admin-bundle/services";

import translateQueueStore from "../../stores/aiTranslatorQueueStore/aITranslatorQueueStore";
import { translate } from "sulu-admin-bundle/utils";
import { Icon } from "sulu-admin-bundle/components";

@observer
class AITranslatorButton extends React.Component {
    @observable valueMemo = "";
    @observable undoBtnVisible = false;
    @observable activeLocale = undefined;
    @observable isDisabled = false;
    @observable isDone = false;

    disposer = null;

    constructor(props) {
        super(props);

        const { formInspector } = props;
        const { formStore } = formInspector;
        const { locale } = formStore;

        this.activeLocale = locale.value;
        // @todo if activeLocale != defaultLocale
        // this.isDisabled = true
    }

    componentDidMount() {
        this.disposer = reaction(
            () => [translateQueueStore.bulkTranslateInProgress], 
            () => {
                if (translateQueueStore.bulkTranslateInProgress) {
                    this.translateField();
                }
            }
        );
    }

    componentWillUnmount() {
        if (this.disposer) {
            this.disposer();
        }
    }

    @action
    translateField() {
        if (this.isDisabled) {
            return;
        }

        const { value } = this.props;
        this.valueMemo = value;

        if (!value || value.trim() === "") {
            this.setInputTranslation("");
            this.undoBtnVisible = false;
            this.isDone = true;
            return;
        }

        this.isDisabled = true;
        translateQueueStore.addActiveQueueItem();

        // Sulu-compatible wrapper around fetch()
        // @see https://jsdocs.sulu.io/2.5/#section-services
        Requester.post("/admin/api/translates", {
            text: value,
            source: null,
            target: this.activeLocale,
        })
            .then(
                action((response) => {
                    const { translation } = response;

                    if (!translation) {
                        return;
                    }

                    this.setInputTranslation(translation);
                    this.undoBtnVisible = true;
                    this.isDone = true;
                })
            )
            .catch((error) => {
                console.error("Error translating item:", error);
                alert(
                    translate("app.translator_error")
                );
            })
            .finally(
                action(() => {
                    translateQueueStore.removeActiveQueueItem();
                    this.isDisabled = false;
                })
            );
    }

    @action
    undoTranslateField() {
        this.undoBtnVisible = false;
        this.setInputTranslation(this.valueMemo);
        this.valueMemo = "";
    }

    setInputTranslation(translation) {
        this.props.onChange(translation);
    }

    render() {
        return (
            <div
                className={classNames(
                    "translator__container",
                    this.isDisabled && "translator__container--disabled"
                )}
            >
                {this.props.children}
                <button
                    className={classNames("translator__btn", this.isDone && "translator__btn--checked")}
                    title={translate("app.translator_translate")}
                    onClick={this.translateField.bind(this)}
                >
                    {this.isDone ? <Icon name="su-check" /> : <Icon name="su-language" />}
                </button>
                {this.undoBtnVisible && (
                    <button
                        className="translator__btn translator__undo-btn"
                        title={translate("app.translator_undo_translation")}
                        onClick={this.undoTranslateField.bind(this)}
                    >
                        <Icon name="fa-rotate-left" />
                    </button>
                )}
            </div>
        );
    }
}

export default AITranslatorButton;
