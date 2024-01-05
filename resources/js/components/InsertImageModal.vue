<template>
  <Modal
    :show="show"
    size="3xl"
    @close-via-escape="warnCloseInsert"
  >
    <form
      @submit.prevent="submit"
      class="mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden"
    >
      <slot>
        <ModalHeader v-text="'Insert Image'"/>
        <ModalContent>
          <div class="flex flex-col md:flex-row" index="0">
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
              <label for="title-introduction-text-field" class="inline-block pt-2 leading-tight">
                Position
              </label>
            </div>
            <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 md:w-4/5 w-full md:py-5">
              <div class="space-y-1">
                <select class="w-full form-control form-input form-input-bordered" v-model="position"
                        @change="dirty = true">
                  <option value="left">Left Align</option>
                  <option value="right">right Align</option>
                  <option value="fullwidth">Full Width</option>
                </select>
              </div>
            </div>
          </div>

          <div class="flex flex-col md:flex-row" index="0">
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
              <label for="title-introduction-text-field" class="inline-block pt-2 leading-tight">
                Description
              </label>
            </div>
            <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 md:w-4/5 w-full md:py-5">
              <div class="space-y-1">
                <textarea class="w-full h-auto py-3 form-control form-input form-input-bordered" v-model="description"
                          @change="dirty = true"/>
              </div>
            </div>
          </div>
        </ModalContent>
      </slot>

      <ModalFooter>
        <div class="ml-auto">
          <LinkButton
            type="button"
            data-testid="cancel-button"
            dusk="cancel-delete-button"
            @click.prevent="warnCloseInsert"
            class="mr-3"
          >
            Cancel
          </LinkButton>

          <DefaultButton type="submit">
            Insert
          </DefaultButton>
        </div>
      </ModalFooter>
    </form>
  </Modal>
</template>

<script>
export default {
  props: {
    show: {
      required: true,
      type: Boolean
    },
    image: {
      required: true,
      type: Object
    },
    field: {
      required: true,
      type: Object,
    }
  },

  data: () => ({
    position: 'left',
    description: '',
    dirty: false,
  }),

  emits: ['close', 'submit'],

  methods: {
    warnCloseInsert() {
      if (this.dirty && !confirm('Are you sure you want to cancel?')) {
        return;
      }

      this.position = 'left';
      this.description = '';
      this.dirty = false;

      this.$emit('close');
    },

    submit() {
      this.$emit('submit', {position: this.position, description: this.description});

      this.position = 'left';
      this.description = '';
      this.dirty = false;
    }
  }
}
</script>
