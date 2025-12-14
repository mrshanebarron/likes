import LdReactions from './LdReactions.vue'

export { LdReactions }

export interface ReactionConfig {
  name: string
  emoji: string
  lottie?: string
  color: string
}

export interface TopReaction {
  reaction: string
  count: number
}

export interface ReactionUpdatedEvent {
  reaction: string | null
  totalCount: number
}

export default LdReactions
