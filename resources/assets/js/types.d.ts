declare let VITE_KOEL_ENV: '' | 'demo'

declare module '*.vue'

declare module '*.jpg' {
  const value: string
  export default value
}

declare module '*.png' {
  const value: string
  export default value
}

declare module '*.svg' {
  const value: string
  export default value
}

declare type Closure = (...args: Array<unknown | any>) => unknown | any

declare module 'alertify.js' {
  function alert (msg: string): void

  function confirm (msg: string, okFunc: Closure, cancelFunc?: Closure): void

  function success (msg: string, cb?: Closure): void

  function error (msg: string, cb?: Closure): void

  function log (msg: string, cb?: Closure): void

  function logPosition (position: string): void

  function closeLogOnClick (close: boolean): void
}

declare module 'sketch-js' {
  function create (config: Record<string, any>): any
}

declare module 'youtube-player' {
  import { YouTubePlayer } from 'youtube-player/dist/types'

  function createYouTubePlayer (name: string, options: Record<string, any>): YouTubePlayer

  export default createYouTubePlayer
}

interface Plyr {
  media: HTMLMediaElement

  restart (): void

  play (): void

  pause (): void

  seek (position: number): void

  setVolume (volume: number): void
}

declare module 'plyr' {
  function setup (el: string | HTMLMediaElement | HTMLMediaElement[], options: Record<string, any>): Plyr[]
}

declare module 'ismobilejs' {
  let apple: { device: boolean }
  let any: boolean
  let phone: boolean
}

declare module 'nouislider' {
  function create (el: HTMLElement, config: {
    connect: boolean[]
    start: number
    range: {
      min: number
      max: number
    }
    orientation: 'horizontal' | 'vertical'
    direction: 'ltr' | 'rtl'
  }): void
}

interface Constructable<T> {
  new (...args: any): T
}

interface Window {
  BASE_URL: string
  readonly PUSHER_APP_KEY: string
  readonly PUSHER_APP_CLUSTER: string
  readonly webkitAudioContext: Constructable<AudioContext>
  readonly mozAudioContext: Constructable<AudioContext>
  readonly oAudioContext: Constructable<AudioContext>
  readonly msAudioContext: Constructable<AudioContext>
  readonly MediaMetadata: Constructable<Record<string, any>>
}

interface FileSystemDirectoryReader {
  readEntries (successCallback: Closure, errorCallback?: Closure): FileSystemEntry[]
}

interface FileSystemEntry {
  readonly isFile: boolean
  readonly isDirectory: boolean
  readonly name: string
  readonly fullPath: string
  readonly filesystem: FileSystem

  createReader (): FileSystemDirectoryReader

  file (successCallback: Closure): void
}

type MediaInfoDisplayMode = 'aside' | 'full'
type ScreenHeaderLayout = 'expanded' | 'collapsed'

interface AlbumTrack {
  readonly title: string
  readonly length: number
}

interface AlbumInfo {
  cover: string | null
  readonly tracks: AlbumTrack[]
  wiki?: {
    summary: string
    full: string
  }
  url?: string
}

interface ArtistInfo {
  image: string | null
  bio?: {
    summary: string
    full: string
  }
  url?: string
}

interface Artist {
  type: 'artists',
  readonly id: number
  name: string
  image: string | null
  play_count: number
  album_count: number
  song_count: number
  length: number
  created_at: string
}

interface Album {
  type: 'albums'
  readonly id: number
  artist_id: number
  artist_name: string
  name: string
  cover: string
  thumbnail?: string | null
  play_count: number
  song_count: number
  length: number
  created_at: string
}

interface Song {
  type: 'songs'
  readonly id: string
  album_id: number
  album_name: string
  album_cover: string
  artist_id: number
  artist_name: string
  album_artist_id: number
  album_artist_name: string
  title: string
  readonly length: number
  track: number | null
  disc: number | null
  lyrics: string
  play_count_registered?: boolean
  preloaded?: boolean
  playback_state?: PlaybackState
  play_count: number
  liked: boolean
  play_start_time?: number
  fmt_length?: string
  created_at: string
}

interface SmartPlaylistRuleGroup {
  id: number
  rules: SmartPlaylistRule[]
}

interface SmartPlaylistModel {
  name: 'title' | 'length' | 'created_at' | 'updated_at' | 'album.name' | 'artist.name' | 'interactions.play_count' | 'interactions.updated_at'
  type: 'text' | 'number' | 'date'
  label: string
  unit?: 'seconds' | 'days'
}

interface SmartPlaylistOperator {
  operator: 'is' | 'isNot' | 'contains' | 'notContain' | 'isBetween' | 'isGreaterThan' | 'isLessThan' | 'beginsWith' | 'endsWith' | 'inLast' | 'notInLast'
  label: string
  type?: SmartPlaylistModel['type'] // to override
  unit?: SmartPlaylistModel['unit'] // to override
  inputs?: number
}

interface SmartPlaylistRule {
  id: number
  model: SmartPlaylistModel | SmartPlaylistModel['name']
  operator: SmartPlaylistOperator['operator']
  value: any[]
}

type SmartPlaylistInputTypes = Record<SmartPlaylistModel['type'], SmartPlaylistOperator[]>

type PlaylistType = 'playlist' | 'favorites' | 'recently-played'

interface Playlist {
  type: 'playlists'
  readonly id: number
  name: string
  is_smart: boolean
  rules: SmartPlaylistRuleGroup[]
}

interface YouTubeVideo {
  readonly id: {
    videoId: string
  }

  readonly snippet: {
    title: string
    description: string
    thumbnails: {
      default: {
        url: string
      }
    }
  }
}

interface UserPreferences {
  lastfm_session_key?: string
}

interface User {
  type: 'users'
  id: number
  name: string
  email: string
  is_admin: boolean
  password?: string
  preferences?: UserPreferences
  avatar: string
}

interface Settings {
  media_path?: string
}

interface Interaction {
  type: 'interactions'
  readonly id: number
  readonly song_id: string
  liked: boolean
  play_count: number
}

interface SliderElement extends HTMLElement {
  noUiSlider?: {
    destroy (): void
    on (eventName: 'change' | 'slide', handler: (value: number[], handle: number) => unknown): void
    set (options: number | any[]): void
  }
}

type OverlayState = {
  showing: boolean
  dismissible: boolean
  type: 'loading' | 'success' | 'info' | 'warning' | 'error'
  message: string
}

interface SongRow {
  song: Song
  selected: boolean
}

interface EqualizerPreset {
  id?: number
  name?: string
  preamp: number
  gains: number[]
}

type DragType = 'Song' | 'Album' | 'Artist'

type DragData = {
  type: 'songs' | 'album' | 'artist'
  value: string[] | number
}

declare type PlaybackState = 'Stopped' | 'Playing' | 'Paused'
declare type MainViewName =
  | 'Home'
  | 'Default'
  | 'Queue'
  | 'Songs'
  | 'Albums'
  | 'Artists'
  | 'Favorites'
  | 'RecentlyPlayed'
  | 'Settings'
  | 'Users'
  | 'YouTube'
  | 'Visualizer'
  | 'Profile'
  | 'Album'
  | 'Artist'
  | 'Playlist'
  | 'Upload'
  | 'Search.Excerpt'
  | 'Search.Songs'

declare type ArtistAlbumCardLayout = 'full' | 'compact'

interface AddToMenuConfig {
  queue: boolean
  favorites: boolean
  playlists: boolean
  newPlaylist: boolean
}

interface SongListControlsConfig {
  play: boolean
  addTo: AddToMenuConfig
  clearQueue: boolean
  deletePlaylist: boolean
}

type ThemeableProperty = '--color-text-primary'
  | '--color-text-secondary'
  | '--color-bg-primary'
  | '--color-bg-secondary'
  | '--color-highlight'
  | '--bg-image'
  | '--bg-position'
  | '--bg-attachment'
  | '--bg-size'

interface Theme {
  id: string
  name?: string
  thumbnailColor: string
  thumbnailUrl?: string
  selected?: boolean
  properties?: Partial<Record<ThemeableProperty, string>>
}

type ArtistAlbumViewMode = 'list' | 'thumbnails'

type RepeatMode = 'NO_REPEAT' | 'REPEAT_ALL' | 'REPEAT_ONE'

type SongListType = 'all-songs'
  | 'queue'
  | 'playlist'
  | 'favorites'
  | 'recently-played'
  | 'artist'
  | 'album'
  | 'search-results'

type SongListColumn = 'track' | 'title' | 'album' | 'artist' | 'length'

interface SongListConfig {
  sortable: boolean
  columns: SongListColumn[]
}

type SongListSortField = keyof Pick<Song, 'track' | 'disc' | 'title' | 'album_name' | 'length' | 'artist_name'>

type SortOrder = 'asc' | 'desc'

type MethodOf<T> = { [K in keyof T]: T[K] extends Closure ? K : never; }[keyof T]

interface PaginatorResource {
  data: any[]
  links: {
    next: string | null
  }
  meta: {
    current_page: number
  }
}

type EditSongFormTabName = 'details' | 'lyrics'

type ToastMessage = {
  id: string
  type: 'info' | 'success' | 'warning' | 'danger'
  content: string
  timeout: number // seconds
}
