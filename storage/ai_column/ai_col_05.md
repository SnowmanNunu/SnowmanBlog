# RAG 检索增强生成：从原理到落地实战

LLM 的知识截止于训练数据，无法回答训练之后的事件，也无法访问私有数据。**RAG（Retrieval-Augmented Generation）**通过在生成前检索相关信息，解决了这一痛点。

---

## RAG 的核心流程

RAG 的工作流程可以概括为三步：

```
用户提问 → 检索相关文档 → 将文档注入 Prompt → LLM 生成回答
```

与传统微调（Fine-tuning）相比，RAG 的优势在于：

| 维度 | 微调 | RAG |
|------|------|-----|
| 数据更新 | 需要重新训练 | 实时更新向量库 |
| 成本 | 高（算力+时间） | 低（仅索引新文档） |
| 幻觉控制 | 较差 | 较好（回答有据可查） |
| 适用场景 | 固定知识、风格迁移 | 动态知识库、文档问答 |

---

## 关键组件

### 1. 文档切分（Chunking）

长文档不能直接 embedding，需要切成固定大小的块。切分策略直接影响检索质量：

- **固定长度**：每 500 Token 切一块，简单但可能切断语义
- **递归切分**：先按段落切，超长再按句子切，保留结构
- **语义切分**：根据语义边界自动切分（如使用 NLTK）

**最佳实践**：代码按函数切、Markdown 按标题切、普通文本按段落切。

### 2. Embedding 模型

将文本转换为向量的模型。常用选择：

| 模型 | 维度 | 特点 |
|------|------|------|
| text-embedding-3-large | 3072 | OpenAI 最强，多语言优秀 |
| text-embedding-ada-002 | 1536 | 性价比高，生态成熟 |
| bge-large-zh | 1024 | 中文场景首选，开源免费 |

### 3. 向量数据库

存储和检索向量的数据库：

| 数据库 | 特点 |
|--------|------|
| Pinecone | 托管服务，无需运维 |
| Weaviate | 支持混合搜索（向量+关键词） |
| Chroma | 轻量级，本地开发首选 |
| pgvector | PostgreSQL 扩展，已有 PG 直接用 |

---

## 实战：搭建一个文档问答系统

以 Node.js + Chroma + OpenAI Embedding 为例：

### 1. 安装依赖

```bash
npm install chromadb openai
```

### 2. 文档索引

```typescript
import { ChromaClient } from 'chromadb';
import OpenAI from 'openai';

const client = new ChromaClient();
const openai = new OpenAI();

// 创建集合
const collection = await client.createCollection({ name: 'docs' });

// 读取文档并切分
const chunks = splitDocument(readFileSync('./api-doc.md', 'utf-8'));

// 生成 Embedding 并入库
for (let i = 0; i < chunks.length; i++) {
  const embed = await openai.embeddings.create({
    model: 'text-embedding-3-small',
    input: chunks[i],
  });

  await collection.add({
    ids: [`chunk-${i}`],
    embeddings: [embed.data[0].embedding],
    documents: [chunks[i]],
  });
}
```

### 3. 查询回答

```typescript
async function ask(question: string) {
  // 1. 问题向量化
  const qEmbed = await openai.embeddings.create({
    model: 'text-embedding-3-small',
    input: question,
  });

  // 2. 检索最相关的 3 个文档块
  const results = await collection.query({
    queryEmbeddings: [qEmbed.data[0].embedding],
    nResults: 3,
  });

  // 3. 组装 Prompt
  const context = results.documents[0].join('\n---\n');
  const prompt = `基于以下文档回答问题。如果文档中没有相关信息，请明确说明。

文档：
${context}

问题：${question}`;

  // 4. 调用 LLM
  const response = await anthropic.messages.create({
    model: 'claude-sonnet-4-6-20251022',
    max_tokens: 1024,
    messages: [{ role: 'user', content: prompt }],
  });

  return response.content[0].text;
}
```

---

## 进阶优化技巧

### 1. 重排序（Re-ranking）

向量检索返回的 Top-K 不一定最相关。使用重排序模型二次精排：

```typescript
// 先用向量召回 20 个候选
const candidates = await collection.query({ queryEmbeddings: [embed], nResults: 20 });

// 再用 Cohere Rerank 精排
const reranked = await cohere.rerank({
  documents: candidates.documents[0],
  query: question,
  top_n: 3,
});
```

### 2. 查询改写（Query Rewriting）

用户的问题可能与文档表述不一致。先让 LLM 改写查询：

```
用户问："怎么登录？"
改写后："用户登录流程、认证方式、登录接口调用方法"
```

### 3. 元数据过滤

为文档块添加元数据（来源、版本、权限），检索时先过滤：

```typescript
await collection.query({
  queryEmbeddings: [embed],
  where: { version: 'v2.0', category: 'api' },
  nResults: 5,
});
```

---

## 写在最后

RAG 不是银弹，它的效果上限取决于三个因素：**文档质量、切分策略、检索精度**。再好的 LLM 也救不了垃圾输入。

一个实用的判断标准：如果人类在文档中也找不到答案，那 RAG 大概率也找不到。

下一篇，我们将探讨 **AI 工作流编排**——如何让多个 Agent 协作完成复杂任务。
