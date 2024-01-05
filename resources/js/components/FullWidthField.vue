<template>
  <field-wrapper>
    <div class="w-full py-6 flex">
      <div class="px-8 w-full md:w-1/5">
        <form-label :for="field.attribute" :class="{
                      'mb-2': field.helpText && showHelpText
                  }">
          {{ fieldLabel }}&nbsp;<span
          v-if="field.required"
          class="text-danger text-sm"
        >{{ __('*') }}</span
        >
        </form-label>

        <help-text :show-help-text="showHelpText">
          {{ field.helpText }}
        </help-text>
      </div>

      <div class="w-full md:w-4/5">
        <slot name="field"/>
      </div>
    </div>
  </field-wrapper>
</template>

<script>
// todo: extend from `default-field` somehow
export default {
  props: {
    field: {type: Object, required: true},
    fieldName: {type: String},
    showHelpText: {type: Boolean, default: true},
  },

  computed: {
    fieldLabel() {
      // If the field name is purposefully an empty string, then
      // let's show it as such
      if (this.fieldName === '') {
        return ''
      }

      return this.fieldName || this.field.singularLabel || this.field.name
    },
  },
};
</script>
