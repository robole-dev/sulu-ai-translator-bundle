import React from "react";
import AITranslatorButton from "./AITranslatorButton";

// A field can opt out of the translation via <param name="ai_translator" value="false"/> in the form xml.
// Sulu passes the value as string, but a boolean is accepted as well.
const isTranslationDisabled = (schemaOptions) => {
    const value = schemaOptions?.ai_translator?.value;

    return (
        value === false ||
        (typeof value === "string" &&
            ["false", "0"].includes(value.trim().toLowerCase()))
    );
};

const withAITranslatorButton = (Component) => {
    return class extends React.Component {
        render() {
            if (isTranslationDisabled(this.props.schemaOptions)) {
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
