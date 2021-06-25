const groupModifiers = (group) => {
  let modifiers = [];
  const avgLength = group.contentLength / group.messages.length

  if (group.contentLength < 100) {
    modifiers = ['normal'];
  } else if (group.contentLength < 1000) {
    modifiers = ['small'];
  } else {
    modifiers = ['tiny'];
  }

  if (avgLength < 50) {
    if (group.messages.length < 20 && group.messages.length >= 10) {
      modifiers.push('columns-2')
    } else if (group.messages.length >= 21) {
      modifiers.push('columns-3')
    }
  }

  return modifiers;
}

const groupMessages = (messages, { maxTimeToPreviousPost }) => {
  if (!messages.length) {
    return [];
  }

  const clonedMessages = JSON.parse(JSON.stringify(messages))
  const grouped = [];
  let currentGroup = {
    author: clonedMessages[0].author,
    postedAt: clonedMessages[0].postedAt,
    messages: [clonedMessages[0]]
  };

  const t = maxTimeToPreviousPost * 1000;
  for (let i = 1; i < clonedMessages.length; i++) {
    const currentMessage = clonedMessages[i];
    const timeToPreviousPost = currentMessage.postedAt - currentGroup.messages[currentGroup.messages.length - 1].postedAt;
    if (currentGroup.author !== currentMessage.author || timeToPreviousPost > t) {
      currentGroup.contentLength = currentGroup.messages.reduce((total, el) => total + el.content.length, 0)
      grouped.push(currentGroup)
      currentGroup = {
        author: currentMessage.author,
        postedAt: currentMessage.postedAt,
        messages: [currentMessage]
      }
    } else {
      currentGroup.messages.push(currentMessage)
    }
  }
  currentGroup.contentLength = currentGroup.messages.reduce((total, el) => total + el.content.length, 0)
  grouped.push(currentGroup);

  return grouped;
};

export default {
  functional: true,
  props: {
    messages: Array,
    maxTimeToPreviousPost: {
      type: Number,
      default: 600
    }
  },
  render(h, { scopedSlots, props }) {
    const groups = groupMessages(props.messages, {
      maxTimeToPreviousPost: props.maxTimeToPreviousPost
    });

    console.log()

    return groups.map((group) => (
      scopedSlots ?
        scopedSlots.default({
          author: group.author,
          postedAt: group.postedAt,
          messages: group.messages,
          modifiers: groupModifiers(group)
        })
        : null
      ))
  }
}
