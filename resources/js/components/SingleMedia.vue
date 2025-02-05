<template>
  <gallery-item class="gallery-item-image" :class="{ 'show-statistics': field.showStatistics }">
    <div class="gallery-item-info">
      <div class="icons p-3">
        <a v-if="insertable" class="icon insertable" title="Insert" @click="showInsertImage = true">
          <Icon name="plus" view-box="0 0 20 22" width="24" height="24"/>
        </a>
        <a v-if="removable" class="icon delete" href="#" @click.prevent="$emit('remove')" title="Remove">
          <Icon name="trash" view-box="0 0 20 20" width="24" height="24"/>
        </a>
      </div>

      <a class="preview" href="#" @click.prevent="showPreview">
        <Icon name="magnifying-glass" view-box="0 0 20 20" width="30" height="30"/>
      </a>

      <span v-text="image.name" class="absolute bg-primary-500 bottom-0 px-2 text-center w-full filename overflow-hidden" />
    </div>

    <img :src="src" :alt="image.name" ref="image" class="gallery-image">

  </gallery-item>

  <Modal
    :show="showFullImage"
    size="7xl"
    @close-via-escape="closePreview"
  >
    <div>
      <ModalHeader class="bg-gray-100 dark:bg-gray-700 flex justify-end">
        <span @click="closePreview">X</span>
      </ModalHeader>

      <ModalContent>
        <img :src="previewImagePath"/>
      </ModalContent>
    </div>
  </Modal>

  <InsertImageModal
    :show="showInsertImage"
    @close="closeInsert"
    @submit="handleInsertImage"
  />
</template>

<script>
import { Icon } from 'laravel-nova-ui'
import GalleryItem from './GalleryItem';
import InsertImageModal from "./InsertImageModal";

export default {
  components: {
    InsertImageModal,
    Icon,
    GalleryItem,
  },
  mounted() {
    console.log(this.image);
  },
  props: ['image', 'field', 'removable', 'editable', 'isCustomPropertiesEditable'],
  data: () => ({
    acceptedMimeTypes: ['image/jpg', 'image/jpeg', 'image/png'],
    src: "data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==",
    width: undefined,
    height: undefined,
    aspectRatio: undefined,
    ratio: undefined,
    size: undefined,
    showFullImage: false,
    previewImagePath: null,
    showInsertImage: false,
  }),
  computed: {
    downloadUrl() {
      if (!this.field.downloadable) {
        return false;
      }

      return this.image.id ? `/nova-vendor/ebess/advanced-nova-media-library/download/${this.image.id}?uuid=${this.image.uuid}` : null;
    },

    insertable() {
      return !!this.field.insertable;
    },
  },
  watch: {
    image: {
      handler: 'getImage',
      immediate: true
    }
  },
  methods: {
    closeInsert() {
      this.showInsertImage = false;
    },

    handleInsertImage({position, description}) {
      console.log({position, description});

      const bodyField = document.getElementById(this.field.insertable);

      const imageHtml = `<article-image src="${this.image.file_name}" title="${description}" position="${position}"></article-image>`;

      const cursorPosition = bodyField.selectionStart;
      const currentValue = bodyField.value;

      const textBefore = currentValue.substring(0, cursorPosition);
      const textAfter = currentValue.substring(cursorPosition, currentValue.length);

      bodyField.value = textBefore + imageHtml + textAfter;

      this.showInsertImage = false;
    },

    showPreview() {
      this.previewImagePath = this.image.file ? URL.createObjectURL(this.image.file) : this.image.__media_urls__.preview;
      this.showFullImage = true;
    },

    closePreview() {
      this.showFullImage = false;
      this.previewImagePath = null;
    },

    getImage() {
      if (this.editable && this.image.__media_urls__.form) {
        this.src = this.image.__media_urls__.form;
      } else if (!this.editable && this.image.__media_urls__.detailView) {
        this.src = this.image.__media_urls__.detailView;
      } else if (this.isVideo(this.image.__media_urls__.__original__)) {
        //Seconds to seek to, to get thumbnail of video
        let seconds = 1;  //TODO get this from the field instead of hardcoding it here
        this.getVideoThumbnail(this.image.__media_urls__.__original__, seconds);
      } else {
        this.src = this.image.__media_urls__.__original__;
      }

      if (this.field.showStatistics) {
        setTimeout(this.calculateStatistics);
      }
    },

    getVideoThumbnail(path, secs = 0) {
      const video = document.createElement('video');
      video.onloadedmetadata = () => {
        video.currentTime = Math.min(Math.max(0, (secs < 0 ? video.duration : 0) + secs), video.duration);
      };
      video.onseeked = (e) => {
        const canvas = document.createElement('canvas');
        canvas.height = video.videoHeight;
        canvas.width = video.videoWidth;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        this.src = canvas.toDataURL();
      };
      video.src = path;
    },

    isVideo(mediaPath) {
      if (mediaPath.startsWith("data:video"))
        return true;
      //TODO better video detection
      const supportedExtensions = [".mp4", ".ogv", ".webm"];
      return supportedExtensions.some((suffix) => {
        return mediaPath.endsWith(suffix)
      });
    },

    calculateStatistics() {
      if (this.$refs.image.complete) {
        this.width = this.$refs.image.naturalWidth;
        this.height = this.$refs.image.naturalHeight;
        this.ratio = Math.round((this.width / this.height) * 100) / 100;

        const gcd = this.gcd(this.width, this.height);
        this.aspectRatio = (this.width / gcd) + ':' + (this.height / gcd);

        if (this.field.showStatistics) {
          const src = this.$refs.image.currentSrc;

          if (src.startsWith('data:')) {
            const base64Length = src.length - (src.indexOf(',') + 1);
            const padding = (src.charAt(src.length - 2) === '=') ? 2 : ((src.charAt(src.length - 1) === '=') ? 1 : 0);
            this.size = this.formatBytes(base64Length * 0.75 - padding);
          } else if (window.performance !== undefined) {
            const imgResourceTimings = window.performance.getEntriesByName(this.$refs.image.currentSrc);
            if (imgResourceTimings.length) {
              const decodedBodySize = imgResourceTimings[0].decodedBodySize;
              if (decodedBodySize) {
                this.size = this.formatBytes(imgResourceTimings[0].decodedBodySize);
              } else {
                this.size = undefined;
              }
            } else {
              this.size = undefined;
            }
          }
        }
      } else {
        this.$refs.image.onload = this.calculateStatistics;
      }
    },

    gcd(a, b) {
      if (b === 0) {
        return a;
      }

      return this.gcd(b, a % b);
    },

    formatBytes(bytes, decimals = 2) {
      if (bytes === 0) return '0 Bytes';

      const k = 1024;
      const dm = decimals < 0 ? 0 : decimals;
      const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

      const i = Math.floor(Math.log(bytes) / Math.log(k));

      return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
  },
};
</script>

<style lang="scss">
$item-max-width: 250px;
$item-max-height: 150px;
$border-radius: 10px;

$item-multiple-max-width: 130px;
$item-multiple-max-height: 100px;

.gallery {
  &.multiple {
    .gallery-item-image.gallery-item {
      width: $item-multiple-max-width;
      height: $item-multiple-max-height;
    }
  }
}

.gallery {
  .gallery-item-image.gallery-item {
    width: $item-max-width;
    height: $item-max-height;

    &:hover .gallery-item-info {
      display: flex;
    }

    &.show-statistics {
      padding-top: 22px;
      padding-bottom: 43px;
      height: #{$item-max-height + 23px};
    }

    .gallery-item-info {
      display: none;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      background-color: rgba(var(--colors-gray-100), 0.7);;
      border-radius: $border-radius;
      position: absolute;
      z-index: 10;
      top: 0;
      bottom: 0;
      left: 0;
      right: 0;

      .icons {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
      }

      .insertable {
        color: var(--colors-white);
      }

      .preview {
        color: rgb(var(--colors-primary-500));
        cursor: zoom-in;
      }

      .delete {
        right: 10px;
        color: rgb(var(--colors-red-500));
      }

      .filename {
        background-color: rgba(128, 204, 252, 0.5);
        text-overflow: ellipsis;
        text-wrap: nowrap;
      }
    }

    .gallery-image {
      object-fit: contain;
      display: block;
      max-height: 100%;
      border-radius: $border-radius;
    }

    .statistics,
    .type {
      position: absolute;
      left: 0;
      width: 100%;
      font-size: .75rem;
      line-height: 0.95;
      text-align: center;
    }

    .statistics {
      bottom: 1px;

      .dimensions {
        font-size: .675rem;
      }

      .ratio {
        font-size: .6rem;
      }
    }

    .type {
      top: 3px
    }
  }

  .icon {
    cursor: pointer;
    position: absolute;
    top: 10px;
    color: rgb(var(--colors-black));
  }

  .edit {
    left: 10px;
  }

  .download {
    left: 10px;
  }
}

.dark {
  .gallery {
    .gallery-item-image.gallery-item {
      .gallery-item-info {
        background-color: rgba(var(--colors-gray-900), 0.9);
      }
    }
  }
}
</style>
