<p class="help-message">
	<?php _e("A field is a set of two data items linked together: an attribute and a value.", MP_TD) ?>
    <a href="https://docs.meilisearch.com/guides/main_concepts/documents.html#fields" target="_blank">
		<?php _e("Read more on MeiliSearch docs.") ?>
    </a>
    <br>
    <?php _e("In a dataset, some fields are more relevant to the search than others. A title, for example, has a value more meaningful to a movie search than its description or its director name.", MP_TD) ?>
    <?php _e('reorder your fields to rank them by most important to the least important fields. <b>fields with the option "searchable" turned off, will be ignored.</b>', MP_TD) ?>
</p>

<div class="mp-index-fields">

    <component v-for="(field, index) in form.data.fields"
               v-model="field.form"
               :settings="field.settings"
               :order="index"
               :key="field.form.id"
               :index="form.data"
               @remove="removeField"
               :is="field.settings.component">
    </component>

</div>

<div class="mp-index-fields-add">

    <select v-model="selectedFieldType">
        <option value=""><?php _e("Select field type", MP_TD); ?></option>
        <option v-for="field in availableFields"
                :value="field.form.id">
            {{ field.settings.fieldName }}
        </option>
    </select>
    <mp-button @click.native.prevent="addField"
               :disabled="! selectedFieldType"
               primary large>
        <?php _e("Add field", MP_TD) ?>
    </mp-button>

</div>