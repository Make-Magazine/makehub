<script>
import { mapState } from 'vuex'
import { Mixins } from 'vimeography-blueprint'

const defaultTemplate = `
  <figure class="vimeography-thumbnail">
    <router-link class="vimeography-link" :to="this.query" :title="video.name" exact exact-active-class="vimeography-link-active" v-on:click.native="scrollToTop">
      <img class="vimeography-thumbnail-img" :src="thumbnailUrl" :alt="video.name" />
    </router-link>
    <figcaption class="vimeography-title">{{video.name}}</figcaption>
  </figure>
`;

const userTemplate = document.querySelector('#vimeography-greyscale-thumbnail');

const Thumbnail = {
  props: ['video'],
  mixins: [Mixins.Thumbnail],
  template: userTemplate ? userTemplate.innerText : defaultTemplate,
  methods: {
    scrollToTop: function () {
      document.querySelector(".vimeography-player").scrollIntoView({behavior: "smooth"});
    }
  },
  computed: {
    query() {
      const q = {
        ...this.$route.query,
        vimeography_gallery: this.$store.state.gallery.id,
        vimeography_video: this.video.id
      };

      return '?' + Object.keys(q).map(k => k + '=' + encodeURIComponent(q[k])).join('&')
    },
    ...mapState({
      galleryId: state => state.id
    })
  }
}

export default Thumbnail;
</script>

<style lang="scss" scoped>
  .vimeography-thumbnail {
    margin: 0;
    padding: 0;
    width: 100%;
    position: relative;
  }

  /* IE11 Support */
  @media all and (-ms-high-contrast: none), (-ms-high-contrast: active) {
    .vimeography-thumbnail {
      flex: 0 0 25%;
    }
  }

  .vimeography-link {
    display: block;
  }

  .vimeography-link-active {
  }

  .vimeography-thumbnail-img {
    padding: 0;
    margin: 0;
    display: block;

    box-shadow: #000 0em 0em 0em; /* Fixes weird firefox rendering with opacity changes */
    filter: url("data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\'><filter id=\'grayscale\'><feColorMatrix type=\'matrix\' values=\'0.3333 0.3333 0.3333 0 0 0.3333 0.3333 0.3333 0 0 0.3333 0.3333 0.3333 0 0 0 0 0 1 0\'/></filter></svg>#grayscale"); /* Firefox 10+ */
    filter: gray; /* IE6-9 */
    -webkit-filter: grayscale(100%); /* Chrome 19+ & Safari 6+ */
    -webkit-backface-visibility: hidden; /* Fix for transition flickering */

    transition: all 0.6s ease;
    max-width: 100%;
    height: auto;
    width: 100%;

    &:hover {
      filter: url("data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\'><filter id=\'grayscale\'><feColorMatrix type=\'matrix\' values=\'1 0 0 0 0, 0 1 0 0 0, 0 0 1 0 0, 0 0 0 1 0\'/></filter></svg>#grayscale");
      -webkit-filter: grayscale(0%);
    }
  }

  .vimeography-title {
    text-align: left;
    overflow: hidden;
    position: absolute;
    bottom: 0;
    left: 0;
    font-family: sans-serif;
    overflow: hidden;
    display: block;
    margin: .375rem 0; /* 6px */
    padding: .185rem .375rem; /* 3px 6px */
    width: auto;
    color: #343434;
    background-color: #fafafa;
    font-size: 0.8rem; /* 15.2px */
    line-height: 1.2rem; /* 20px */
    font-weight: 600;
    -webkit-font-smoothing: antialiased;
  }
</style>
