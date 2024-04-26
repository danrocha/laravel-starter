import { defaultConfig } from "@formkit/vue";
import { genesisIcons } from "@formkit/icons";
import { rootClasses } from "./formkit.theme";
import { createProPlugin, rating, toggle } from "@formkit/pro";

// Create the Pro plugin with your `Project Key` and desired Pro Inputs:
const proPlugin = createProPlugin(import.meta.VITE_FORMKIT_PRO_KEY, {
    // add inputs here
});

export default defaultConfig({
    config: {
        rootClasses,
    },
    plugins: [proPlugin],
    icons: {
        ...genesisIcons,
    },
});
