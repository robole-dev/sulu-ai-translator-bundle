import React from "react";
import AITranslatorButton from "./AITranslatorButton";
import { isTranslationAllowed } from "../../permissions";

const withAITranslatorButton = (Component) => {
    return class extends React.Component {
        render() {
            if (!isTranslationAllowed(this.props.formInspector?.resourceKey)) {
                return <Component {...this.props} />;
            }

            return (
                <AITranslatorButton {...this.props}>
                    <Component {...this.props} />
                </AITranslatorButton>
            );
        }
    };
};

export default withAITranslatorButton;
