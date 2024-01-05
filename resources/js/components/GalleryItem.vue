<template>
  <div class="gallery-item">
    <slot/>
  </div>
</template>

<script>
  export default {
    props: ['image', 'thumbnail', 'removable'],
  };
</script>

<style lang="scss">
  $bg-color: rgba(var(--colors-gray-100));
  $bg-color-dark: rgba(var(--colors-gray-900));

  $border-radius: 10px;

  .gallery {
    &.editable {
      .gallery-item {
        cursor: zoom-in;
      }
    }

    .gallery-item {
      float: left;
      display: flex;
      flex-direction: column;
      justify-content: center;
      position: relative;
      border-radius: $border-radius;
      background-color: $bg-color;

      .gallery-item-info {
        display: flex;
        background-color: $bg-color;
        border-radius: $border-radius;
        z-index: 10;
      }
    }
  }

  .dark {
    .gallery {
      .gallery-item {
        background-color: $bg-color-dark;
      }
    }
  }
</style>
