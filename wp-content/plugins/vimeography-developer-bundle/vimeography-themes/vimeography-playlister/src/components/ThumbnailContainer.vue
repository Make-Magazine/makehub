<script>
  import { mapActions, mapState } from 'vuex'
  import VueScrollTo from 'vue-scrollto';
  import Spinner from 'vue-simple-spinner'

  import Thumbnail from './Thumbnail.vue';

  const template = `
    <div class="vimeography-thumbnail-container" @scroll="handleScroll">
      <thumbnail
        v-for="(video, index) in videos"
        v-bind:video="video"
        v-bind:index="index"
        v-bind:key="video.id">
      </thumbnail>

      <spinner size="small" v-show="this.loading"></spinner>
    </div>
  `;

  const ThumbnailContainer = {
    props: ['videos', 'activeVideoId'],
    template,
    components: {
      Thumbnail,
      Spinner
    },
    computed: {
      ...mapState({
        pro: state => state.gallery.pro,
        loading: state => state.videos.loading
      }),
    },
    methods: {
      ...mapActions([
        'paginate',
      ]),
      handleScroll: function(e) {
        let currentScrollPosition = e.srcElement.scrollTop;
        let totalElementHeight = e.srcElement.scrollHeight;
        let scrollPaneHeight = e.srcElement.clientHeight;

        let scrollOffset = currentScrollPosition + scrollPaneHeight;
        let progress = (scrollOffset / totalElementHeight);

        if (! this.pro) {
          console.log('Vimeography PRO is not installed, pagination is unavailable.')
          return;
        }

        let paging = this.$store.getters.paging

        // console.log('Vimeography: playlister gallery scroll progress is ' + progress );

        if ( progress < 0.25 ) {
          this.paginate( paging.previous );
        }

        if ( progress > 0.75 ) {
          this.paginate( paging.next );
        }
      }
    },
    // mounted: function() {
    //   let index = this.$store.getters.getVideoIndex(this.activeVideoId);
    //   let element = this.$children[index].$el;

    //   element.scrollIntoView({
    //     behavior: 'smooth',
    //     block: 'start',
    //     inline: 'start'
    //   });
    // },
    watch: {
      activeVideoId(id) {
        let index = this.$store.getters.getVideoIndex(id);
        let element = this.$children[index].$el;

        this.$scrollTo(element, 300, { container: this.$el })
      }
    },
  }

  export default ThumbnailContainer;
</script>

<style lang="scss" scoped>
  .vimeography-thumbnail-container {
    position: relative;
    max-height: 240px;
    overflow-y: auto;
    background-color: #444;

    /deep/ .vue-simple-spinner {
      margin: 1rem auto !important;
    }
  }

  @media all and (min-width: 940px) {
    .vimeography-thumbnail-container {
      max-height: initial;
      position: absolute;
      grid-column-start: 2;
      top: 0;
      bottom: 0;
      left: 0;
      right: 0;
    }
  }

  @media all and (-ms-high-contrast: none), (-ms-high-contrast: active), (min-width: 940px) {
    .vimeography-thumbnail-container {
      flex: 0 1 45%;
      left: 55%;
      overflow-x: hidden;
    }
  }
</style>
