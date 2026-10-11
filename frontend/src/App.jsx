import { AnimatePresence, motion } from 'framer-motion'
import {
  BadgeCheck,
  Bell,
  Heart,
  Home,
  MapPin,
  MessageCircle,
  Shield,
  Sparkles,
  User,
  Wallet,
  X,
} from 'lucide-react'
import { useState } from 'react'
import './index.css'

const areas = ['Civil Lines', 'Prem Nagar', 'Rajendra Nagar', 'Izzat Nagar', 'Model Town']

const profiles = [
  {
    id: 1,
    name: 'Aarohi',
    age: 24,
    area: 'Civil Lines',
    distance: '3.2 km',
    verified: true,
    online: true,
    interests: ['Music', 'Cafe hopping', 'Family first'],
    photo:
      'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=900&q=80',
  },
  {
    id: 2,
    name: 'Meera',
    age: 27,
    area: 'Prem Nagar',
    distance: '5.8 km',
    verified: true,
    online: false,
    interests: ['Books', 'Street food', 'Long walks'],
    photo:
      'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=900&q=80',
  },
  {
    id: 3,
    name: 'Naina',
    age: 25,
    area: 'Rajendra Nagar',
    distance: '7.4 km',
    verified: false,
    online: true,
    interests: ['Fitness', 'Movies', 'Travel'],
    photo:
      'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=900&q=80',
  },
]

const packages = [
  ['One Day Pass', 'Rs 39', '50 credits', '1 day'],
  ['Starter', 'Rs 99', '150 credits', '7 days'],
  ['Popular', 'Rs 299', '600 credits', '30 days', 'MOST POPULAR'],
  ['Premium', 'Rs 399', '1100 credits', '45 days + badge + likes'],
]

function App() {
  const [active, setActive] = useState('Discover')
  const [card, setCard] = useState(0)
  const [wallet, setWallet] = useState(10)
  const [area, setArea] = useState('All areas')
  const [liked, setLiked] = useState(() => { try { return JSON.parse(localStorage.getItem('bh-likes')) || [] } catch { return [] } })
  const [notice, setNotice] = useState('')
  const filtered = profiles.filter(p => area === 'All areas' || p.area === area)
  const current = filtered[card % filtered.length]
  const notify = (text) => { setNotice(text); setTimeout(() => setNotice(''), 3500) }

  const screen = (() => {
    if (active === 'Wallet') return <WalletScreen wallet={wallet} notify={notify} />
    if (active === 'Chats') return <ChatsScreen wallet={wallet} setWallet={setWallet} recharge={() => setActive('Wallet')} />
    if (active === 'Likes') return <LikesScreen liked={liked} />
    if (active === 'Profile') return <ProfileScreen />
    return <DiscoverScreen profile={current} area={area} setArea={value => { setArea(value); setCard(0) }} onSwipe={(like) => { if (like && current) { const next = [...new Set([...liked, current.id])]; setLiked(next); localStorage.setItem('bh-likes', JSON.stringify(next)); notify(`You liked ${current.name}. A mutual like unlocks chat.`) } setCard((value) => value + 1) }} />
  })()

  return (
    <div className="min-h-screen bg-[#FFF8F9] text-[#29212f]">
      <div className="mx-auto flex min-h-screen w-full max-w-6xl flex-col lg:flex-row">
        <aside className="hidden w-80 shrink-0 flex-col gap-6 border-r border-pink-100/80 bg-white/70 p-6 backdrop-blur-xl lg:flex">
          <Brand />
          <div className="sidebar-nav">{['Discover', 'Likes', 'Chats', 'Wallet', 'Profile'].map(label => <button className={active === label ? 'selected' : ''} key={label} onClick={() => setActive(label)}>{label}</button>)}</div>
          <SafetyPanel />
        </aside>

        <main className="relative flex min-h-screen flex-1 flex-col overflow-hidden">
          <header className="sticky top-0 z-20 flex items-center justify-between border-b border-pink-100/80 bg-[#FFF8F9]/85 px-4 py-4 backdrop-blur-xl sm:px-8">
            <Brand />
            <div className="flex items-center gap-3">
              <button onClick={() => setActive('Wallet')} className="rounded-full bg-white px-4 py-2 text-sm font-semibold shadow-soft">
                {wallet} credits
              </button>
              <button className="icon-btn" aria-label="Notifications" onClick={() => notify('You are all caught up. New matches will appear here.')}>
                <Bell size={20} />
              </button>
            </div>
          </header>
          <p className="preview-notice">Preview mode: sample profiles and chats. Live login, payments and calls require the backend connection.</p>

          <AnimatePresence mode="wait">
            <motion.section
              key={active + card}
              initial={{ opacity: 0, y: 16 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -12 }}
              transition={{ duration: 0.28 }}
              className="flex-1 px-4 pb-28 pt-5 sm:px-8 lg:pb-8"
            >
              {screen}
            </motion.section>
          </AnimatePresence>

          <BottomNav active={active} setActive={setActive} />
          {notice && <div className="toast" role="status">{notice}</div>}
        </main>
      </div>
    </div>
  )
}

function Brand({ compact = false }) {
  return (
    <div className="flex items-center gap-3">
      <div className="grid h-11 w-11 place-items-center rounded-2xl bg-[#FF6B8A] text-white shadow-pink">
        <Heart fill="currentColor" size={22} />
      </div>
      <div className={compact ? 'hidden sm:block' : ''}>
        <p className="font-display text-xl font-bold leading-tight">Bareilly Hearts</p>
        <p className="text-xs font-semibold uppercase tracking-wide text-[#8A5CFF]">Only for Bareilly</p>
      </div>
    </div>
  )
}

function SafetyPanel() {
  return (
    <section className="safety-panel rounded-[1.25rem] p-5">
      <Shield size={24} />
      <h2 className="mt-4 text-lg font-bold">Your comfort comes first</h2>
      <p className="mt-2 text-sm">Go at your own pace. Keep your location private and choose who gets close. Women always connect for free.</p>
    </section>
  )
}

function DiscoverScreen({ profile, onSwipe, area, setArea }) {
  return (
    <div>
      <div className="discover-heading"><p>ONLY FOR YOUR CITY</p><h1>Meet your kind of person.</h1><span>New faces. Familiar city. A connection that's yours.</span></div>
      <div className="discover-grid">
      <div>
        <div className="mb-5 flex flex-wrap items-center gap-3">
          <label className="area-select"><MapPin size={16} /><select aria-label="Filter by area" value={area} onChange={e => setArea(e.target.value)}>{['All areas', ...areas].map(a => <option key={a}>{a}</option>)}</select></label>
          <span className="verified-label"><BadgeCheck size={15} /> Local profiles</span>
        </div>

        {profile ? <motion.article
          drag="x"
          dragConstraints={{ left: 0, right: 0 }}
          onDragEnd={(_, info) => { if (Math.abs(info.offset.x) > 90) onSwipe(info.offset.x > 0) }}
          whileDrag={{ rotate: 4, scale: 0.98 }}
          className="profile-card relative mx-auto max-w-md overflow-hidden rounded-[2rem] bg-white shadow-card"
        >
          <img className="h-full w-full object-cover" src={profile.photo} alt={`${profile.name} profile`} />
          <div className="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 via-black/25 to-transparent p-6 text-white">
            <div className="mb-3 flex items-center gap-2">
              {profile.verified && <BadgeCheck className="text-[#FFD6E0]" size={22} />}
              <span className="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold backdrop-blur">{profile.online ? 'Active now' : 'Here for a connection'}</span>
              <span className={`h-2.5 w-2.5 rounded-full ${profile.online ? 'bg-emerald-300' : 'bg-white/50'}`} />
            </div>
            <h1 className="font-display text-4xl font-bold">{profile.name}, {profile.age}</h1>
            <p className="mt-2 flex items-center gap-2 text-sm text-white/85">
              <MapPin size={16} /> {profile.area} · {profile.distance}
            </p>
            <div className="mt-4 flex flex-wrap gap-2">
              {profile.interests.map((item) => <span className="rounded-full bg-white/20 px-3 py-1 text-sm backdrop-blur" key={item}>{item}</span>)}
            </div>
          </div>
        </motion.article> : <EmptyState icon={<MapPin size={30} />} title="More connections soon" text="Try another area to explore today's profiles." />}

        <div className="mx-auto mt-6 flex max-w-md items-center justify-center gap-5">
          <button disabled={!profile} className="action-btn bg-white text-[#8A5CFF]" onClick={() => onSwipe(false)} aria-label="Pass"><X size={30} /></button>
          <button disabled={!profile} className="action-btn bg-[#FF6B8A] text-white" onClick={() => onSwipe(true)} aria-label="Like"><Heart fill="currentColor" size={32} /></button>
        </div>
      </div>

      <aside className="space-y-4">
        <div className="connection-copy"><Heart size={25} /><h2>A good connection starts with you</h2><p>No rush. No pressure. Just a chance to meet someone who gets you.</p><div className="local-community">{profiles.map(p => <img src={p.photo} alt="" key={p.id} />)}<span>Closer to home.<br />Only for Bareilly.</span></div></div>
        <SafetyPanel />
      </aside>
      </div>
    </div>
  )
}

function ChatsScreen({ wallet, setWallet, recharge }) {
  const [conversation, setConversation] = useState(null)
  const [draft, setDraft] = useState('')
  const [messages, setMessages] = useState(() => { try { return JSON.parse(localStorage.getItem('bh-chat')) || {} } catch { return {} } })
  const locked = wallet <= 0
  if (conversation) return <div className="chat-panel"><div className="chat-title"><button className="icon-btn" onClick={() => setConversation(null)} title="Back to chats">←</button><img src={conversation.photo} alt="" /><h2>{conversation.name}</h2><span>Sample chat</span></div><div className="message-list"><p className="incoming">Hey! What's your favourite coffee place in Bareilly?</p>{(messages[conversation.id] || []).map((text, i) => <p className="outgoing" key={i}>{text}</p>)}</div><form className="message-form" onSubmit={e => { e.preventDefault(); if (!draft.trim()) return; if (wallet < 2) { recharge(); return } const next = { ...messages, [conversation.id]: [...(messages[conversation.id] || []), draft.trim()] }; setMessages(next); localStorage.setItem('bh-chat', JSON.stringify(next)); setWallet(wallet - 2); setDraft('') }}><input aria-label="Message" placeholder="Write a thoughtful hello..." value={draft} onChange={e => setDraft(e.target.value)} /><button disabled={!draft.trim()} className="primary-btn">Send</button></form><p className="cost-note">Male preview account: 2 credits per message</p></div>
  return (
    <div className="grid gap-4 lg:grid-cols-2">
      {profiles.slice(0, 2).map((profile) => (
        <section className="glass flex gap-4 p-4" key={profile.id}>
          <img className="h-20 w-20 rounded-2xl object-cover" src={profile.photo} alt="" />
          <div className="min-w-0 flex-1">
            <div className="flex items-center gap-2">
              <h2 className="font-display text-xl font-bold">{profile.name}</h2>
              {profile.verified && <BadgeCheck className="text-[#8A5CFF]" size={18} />}
            </div>
            <p className="mt-1 text-sm text-[#776b80]">Match unlocked. Say hello with warmth.</p>
            <div className="mt-4 flex gap-2">
              <button className="primary-btn flex-1" onClick={() => locked ? recharge() : setConversation(profile)}>{locked ? 'Recharge to continue' : 'Open sample chat'}</button>
            </div>
          </div>
        </section>
      ))}
    </div>
  )
}

function LikesScreen({ liked }) {
  return liked.length ? <div><h1 className="screen-title">Your little sparks</h1><div className="likes-grid">{profiles.filter(p => liked.includes(p.id)).map(p => <article key={p.id}><img src={p.photo} alt={p.name} /><h2>{p.name}, {p.age}</h2><p>{p.area}</p><Heart size={20} /></article>)}</div></div> : <EmptyState icon={<Sparkles size={34} />} title="Your next spark is out there" text="Profiles you like will appear here. Discover someone who makes you smile." />
}

function WalletScreen({ wallet, notify }) {
  return (
    <div>
      <section className="mb-5 rounded-[1.5rem] bg-white p-5 shadow-soft">
        <p className="text-sm font-semibold text-[#8A5CFF]">Male wallet</p>
        <h1 className="mt-1 font-display text-4xl font-bold">{wallet} credits</h1>
        <p className="mt-2 text-sm text-[#776b80]">10 welcome credits. Women always connect for free.</p>
      </section>
      <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        {packages.map(([name, price, credit, validity, badge]) => (
          <section className="glass relative p-5" key={name}>
            {badge && <span className="absolute right-4 top-4 rounded-full bg-[#FFD6E0] px-3 py-1 text-xs font-bold text-[#FF3F70]">{badge}</span>}
            <h2 className="font-display text-xl font-bold">{name}</h2>
            <p className="mt-4 text-3xl font-bold">{price}</p>
            <p className="mt-2 text-sm text-[#776b80]">{credit} · valid {validity}</p>
            <button className="primary-btn mt-5 w-full" onClick={() => notify('Payments need the live backend. No payment has been taken.')}>Choose plan</button>
          </section>
        ))}
      </div>
    </div>
  )
}

function ProfileScreen() {
  const [saved, setSaved] = useState(false)
  const [profile, setProfile] = useState(() => { try { return JSON.parse(localStorage.getItem('bh-profile')) || { name: '', bio: '', area: 'Civil Lines' } } catch { return { name: '', bio: '', area: 'Civil Lines' } } })
  return (
    <div className="grid gap-5 lg:grid-cols-[1fr_360px]">
      <section className="glass p-5">
        <p className="section-title">Profile setup</p>
        <form className="profile-form" onSubmit={e => { e.preventDefault(); localStorage.setItem('bh-profile', JSON.stringify(profile)); setSaved(true) }}><label>Name<input required value={profile.name} onChange={e => { setSaved(false); setProfile({ ...profile, name: e.target.value }) }} /></label><label>Area<select value={profile.area} onChange={e => setProfile({ ...profile, area: e.target.value })}>{areas.map(a => <option key={a}>{a}</option>)}</select></label><label>A little about you<textarea maxLength="500" value={profile.bio} onChange={e => setProfile({ ...profile, bio: e.target.value })} placeholder="What makes you smile?" /></label><button className="primary-btn">{saved ? 'Saved on this device' : 'Save profile'}</button></form>
      </section>
      <section className="glass p-5">
        <p className="section-title">Safety controls</p>
        <Toggle label="Hide my location" />
        <p className="mt-4 text-sm text-[#a08ba6]">Incognito is available for female accounts after verified signup.</p>
      </section>
    </div>
  )
}

function BottomNav({ active, setActive }) {
  const items = [
    ['Discover', Home],
    ['Likes', Heart],
    ['Chats', MessageCircle],
    ['Wallet', Wallet],
    ['Profile', User],
  ]
  return (
    <nav className="fixed inset-x-0 bottom-0 z-30 mx-auto grid max-w-2xl grid-cols-5 border-t border-pink-100 bg-white/90 px-2 py-2 backdrop-blur-xl lg:absolute">
      {items.map(([label, Icon]) => (
        <button key={label} className={`nav-btn ${active === label ? 'text-[#FF6B8A]' : 'text-[#8a7f91]'}`} onClick={() => setActive(label)}>
          <Icon size={20} fill={label === 'Likes' && active === label ? 'currentColor' : 'none'} />
          <span>{label}</span>
        </button>
      ))}
    </nav>
  )
}

function Toggle({ label }) {
  const [checked, setChecked] = useState(() => localStorage.getItem('bh-hide-location') !== 'false')
  return <label className="mt-4 flex items-center justify-between rounded-2xl bg-white/70 p-4 font-semibold"><span>{label}</span><input type="checkbox" checked={checked} onChange={e => { setChecked(e.target.checked); localStorage.setItem('bh-hide-location', String(e.target.checked)) }} className="h-5 w-5 accent-[#FF6B8A]" /></label>
}

function EmptyState({ icon, title, text }) {
  return <section className="glass grid min-h-[420px] place-items-center p-8 text-center"><div>{icon}<h1 className="mt-4 font-display text-3xl font-bold">{title}</h1><p className="mx-auto mt-2 max-w-md text-[#776b80]">{text}</p></div></section>
}

export default App
