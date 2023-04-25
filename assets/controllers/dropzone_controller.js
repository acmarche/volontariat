import {Controller} from "@hotwired/stimulus";
import Dropzone from "@symfony/ux-dropzone";

export default class extends Controller {
    connect() {
        Dropzone.options.formdrop = {
            dictDefaultMessage: "Glissez ici vos images ou cliquez sur cette zone pour ajouter des photos",
            init: function () {
                this.on("addedfile", function (file) {

                });
            }
        };
    }
}
