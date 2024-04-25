import React from "react";
import AITranslatorButton from "./AITranslatorButton";

const withAITranslatorButton = (Component) => {
    return class extends React.Component {
        render() {
            return (
                <AITranslatorButton {...this.props}>
                    <Component {...this.props} />
                </AITranslatorButton>
            );
        }
    };
};

export default withAITranslatorButton;
