function PostItem({ post }) {
  return (
    <div style={{ border: '1px solid #ccc', borderRadius: '8px', padding: '16px', marginBottom: '12px' }}>
      <h3>{post.title}</h3>
      <p>{post.body}</p>
      <p style={{ fontSize: '0.85em', color: '#666' }}>
        投稿者: {post.user.name} ／ カテゴリ: {post.category.name}
      </p>
    </div>
  );
}

export default PostItem;