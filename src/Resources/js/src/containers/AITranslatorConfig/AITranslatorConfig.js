import React from "react";
import { observer } from "mobx-react";
import { action, observable, computed } from "mobx";
import {
  Loader,
  CircularProgressbar,
  Button,
} from "sulu-admin-bundle/components";
import { withToolbar } from "sulu-admin-bundle/containers";
import { translate } from "sulu-admin-bundle/utils";
import { Requester } from "sulu-admin-bundle/services";

const formatNumber = (value) => new Intl.NumberFormat("de-DE").format(value);

@observer
class AITranslatorConfig extends React.Component {
  @observable loading = false;
  @observable characterCount = undefined;
  @observable characterLimit = undefined;

  componentDidMount() {
    this.loadData();
  }

  @computed get usagePercentage() {
    if (this.characterCount && this.characterLimit) {
      return parseInt((this.characterCount / this.characterLimit) * 100);
    }

    return undefined;
  }

  @action loadData = () => {
    this.loading = true;
    this.characterCount = undefined;
    this.characterLimit = undefined;

    return Requester.get("/admin/api/translate/usage")
      .then(
        action((response) => {
          if (response.character_count && response.character_limit) {
            this.characterCount = response.character_count;
            this.characterLimit = response.character_limit;
          }
        })
      )
      .catch((e) => {
        console.error("Error while loading usage data from server.", e);
      })
      .finally(
        action(() => {
          this.loading = false;
        })
      );
  };

  onClickDashboardBtn() {
    window.open("https://www.deepl.com/de/your-account/subscription", "_blank");
  }

  render() {
    if (this.loading) {
      return <Loader />;
    }

    return (
      <div>
        <h1>{translate("app.translator_config_headline")}</h1>
        {this.usagePercentage && (
          <CircularProgressbar percentage={this.usagePercentage} size={200} />
        )}
        <div style={{ marginTop: 20, marginBottom: 20 }}>
          {translate("app.translator_config_usage_statistics", {
            characterCount: formatNumber(this.characterCount),
            characterLimit: formatNumber(this.characterLimit),
          })}
        </div>
        <Button skin="primary" onClick={this.onClickDashboardBtn}>
          {translate("app.translator_config_deepl_dashboard")}
        </Button>
      </div>
    );
  }
}

export default withToolbar(AITranslatorConfig, function () {
  return {
    items: [
      {
        type: "button",
        label: translate("app.translator_refresh_statistics"),
        icon: "su-sync",
        disabled: this.loading,
        onClick: () => {
          this.loadData();
        },
      },
    ],
  };
});
